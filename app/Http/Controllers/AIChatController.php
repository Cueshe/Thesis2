<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\User;

class AIChatController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'conversation' => ['array'],
            'conversation.*.role' => ['required', 'string'],
            'conversation.*.content' => ['required', 'string'],
            'language' => ['nullable', 'string', 'in:en,fil,bis'],
        ]);

        $openRouterKey = config('services.openrouter.api_key');
        $geminiKey = config('services.gemini.api_key');
        $language = $validated['language'] ?? 'en';

        if (!$openRouterKey && !$geminiKey) {
            return $this->fallbackResponse(
                $this->buildFallbackMessages($validated['message'], $validated['conversation'] ?? []),
                $language,
                'local_only'
            );
        }

        // Gather system data for the current user
        $systemContext = $this->buildSystemContext();

        $messages = [
            [
                'role' => 'system',
                'content' => $this->buildSystemPrompt($systemContext, $language),
            ],
        ];

        if (!empty($validated['conversation'])) {
            foreach ($validated['conversation'] as $previousMessage) {
                $messages[] = [
                    'role' => $previousMessage['role'] === 'assistant' ? 'assistant' : 'user',
                    'content' => Str::limit($previousMessage['content'], 2000),
                ];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $validated['message']];

        return $openRouterKey
            ? $this->sendViaOpenRouter($messages, $openRouterKey, $language)
            : $this->sendViaGemini($messages, $geminiKey, $language);
    }

    /**
     * @param  array<int, array{role:string,content:string}>  $messages
     */
    protected function sendViaOpenRouter(array $messages, string $apiKey, string $language): JsonResponse
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'HTTP-Referer' => config('app.url', 'http://localhost'),
                'X-Title' => config('app.name', 'Q2L'),
            ])->timeout(20)->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => config('services.openrouter.model', 'openrouter/openai/gpt-4o-mini'),
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 512,
            ]);

            if ($response->successful()) {
                $reply = trim((string) data_get($response->json(), 'choices.0.message.content', ''));

                if ($reply !== '') {
                    return response()->json([
                        'reply' => $reply,
                        'status' => 'ok',
                    ]);
                }
            }

            logger()->warning('OpenRouter chat failure', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->fallbackResponse($messages, $language);
        } catch (\Throwable $exception) {
            report($exception);

            return $this->fallbackResponse($messages, $language);
        }
    }

    /**
     * @param  array<int, array{role:string,content:string}>  $messages
     */
    protected function sendViaGemini(array $messages, string $apiKey, string $language): JsonResponse
    {
        try {
            $model = config('services.gemini.model', 'gemini-2.0-flash-exp');
            $endpoint = sprintf(
                'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
                $model,
                urlencode($apiKey)
            );

            $systemInstruction = array_shift($messages);

            $contents = collect($messages)->map(function (array $message) {
                return [
                    'role' => $message['role'] === 'assistant' ? 'model' : 'user',
                    'parts' => [
                        ['text' => $message['content']],
                    ],
                ];
            })->values()->all();

            $response = Http::timeout(20)->post($endpoint, array_filter([
                'systemInstruction' => [
                    'parts' => [
                        ['text' => data_get($systemInstruction, 'content', '')],
                    ],
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 512,
                ],
            ]));

            if ($response->successful()) {
                $reply = trim((string) data_get($response->json(), 'candidates.0.content.parts.0.text', ''));

                if ($reply !== '') {
                    return response()->json([
                        'reply' => $reply,
                        'status' => 'ok',
                    ]);
                }
            }

            $errorMessage = trim((string) data_get($response->json(), 'error.message', ''));
            $errorCode = data_get($response->json(), 'error.code', $response->status());

            // Handle specific quota error
            if ($errorCode === 429 || str_contains(strtolower($errorMessage), 'quota')) {
                return response()->json([
                    'reply' => "I've reached my daily usage limit for today. Please try again tomorrow, or ask your administrator to upgrade the API plan.",
                    'status' => 'quota_exceeded',
                ], 429);
            }

            logger()->warning('Gemini chat failure', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->fallbackResponse($messages, $language);
        } catch (\Throwable $exception) {
            report($exception);

            return $this->fallbackResponse($messages, $language);
        }
    }

    /**
     * Provide a helpful response when upstream AI services are unavailable.
     */
    protected function fallbackResponse(array $messages, string $language, string $mode = 'fallback'): JsonResponse
    {
        $userMessage = $this->extractLatestUserUtterance($messages);
        $reply = $this->buildFallbackReply($userMessage, $language, $mode);

        return response()->json([
            'reply' => $reply,
            'status' => $mode,
        ]);
    }

    /**
     * Extract the most recent user message from the conversation history.
     */
    protected function extractLatestUserUtterance(array $messages): string
    {
        for ($i = count($messages) - 1; $i >= 0; $i--) {
            if (($messages[$i]['role'] ?? null) === 'user') {
                return trim((string) ($messages[$i]['content'] ?? ''));
            }
        }

        return '';
    }

    /**
     * Build a language-aware fallback reply based on the last user prompt.
     */
    protected function buildFallbackReply(string $userMessage, string $language, string $mode = 'fallback'): string
    {
        $focus = $userMessage !== '' ? Str::limit($userMessage, 140) : 'your current lesson focus';

        $templates = [
            'en' => [
                "Here’s a quick actionable sequence you can run for **{$focus}**:",
                "1. Lead with a one-sentence success target so everyone knows what ‘good’ looks like.",
                "2. Surface 2–3 anchor points (keywords, examples, or misconceptions) you want the class to remember.",
                "3. Run a fast practice loop—think-pair-share, pronunciation drill, or a 3-question mini challenge.",
                "4. Close with a pulse check or exit ticket and note who might need coaching next session.",
                "I’m ready for the next question whenever you are!"
            ],
            'fil' => [
                "Narito ang isang mabilis na plano para sa **{$focus}**:",
                "1. Sabihin sa isang pangungusap ang inaasahang resulta para malinaw sa klase ang target.",
                "2. Itala ang 2–3 pangunahing ideya o bokabularyo na gusto mong magamit nila.",
                "3. Magpasok ng mabilis na aktibidad—think-pair-share, pronunciation drill, o mini quiz—para mailapat agad.",
                "4. Tapusin sa reflection o exit ticket para makita kung sino ang kailangan pang i-coach.",
                "Handa akong tumulong ulit kapag may susunod kang tanong!"
            ],
            'bis' => [
                "Aniay paspas nga playbook para sa **{$focus}**:",
                "1. Sugdi sa usa ka klarong success target aron kabalo dayon ang klase unsay tumong.",
                "2. Pilia ang 2–3 ka pundok sa keywords, mga ehemplo, o posibleng sayop nga gusto nimong masabtan nila.",
                "3. Dagani og mubo nga aktividad—think-pair-share, pronunciation drill, o mini quiz—para ma-praktis nila dayon.",
                "4. Tapusa sa reflection o exit ticket aron mahibalo kinsay kinahanglan pa og follow-up.",
                "Pwede ra ka magpadayon og pangutana, andam ko motabang!"
            ],
        ];

        $lines = $templates[$language] ?? $templates['en'];

        return implode("\n", $lines);
    }

    /**
     * Build a minimal message history when only local fallback is available.
     */
    protected function buildFallbackMessages(string $latestMessage, array $conversation): array
    {
        $messages = [];

        foreach ($conversation as $entry) {
            if (!isset($entry['role'], $entry['content'])) {
                continue;
            }

            $messages[] = [
                'role' => $entry['role'] === 'assistant' ? 'assistant' : 'user',
                'content' => Str::limit($entry['content'], 2000)
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $latestMessage];

        return $messages;
    }

    /**
     * Build system context from database
     */
    protected function buildSystemContext(): array
    {
        $user = auth()->user();
        $context = [
            'user' => null,
            'profile' => null,
            'gamification' => null,
        ];

        if ($user) {
            // Load relationships
            $user->load(['studentProfile', 'teacherProfile']);

            $context['user'] = [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ];

            // Get user profile based on role
            if ($user->role === 'student' && $user->studentProfile) {
                $context['profile'] = [
                    'grade_level' => $user->studentProfile->grade_level,
                    'section' => $user->studentProfile->section,
                ];
            } elseif ($user->role === 'teacher' && $user->teacherProfile) {
                $context['profile'] = [
                    'subject' => $user->teacherProfile->subject,
                    'grade_level' => $user->teacherProfile->grade_level,
                ];
            }

            // Get gamification data
            $context['gamification'] = [
                'level' => $user->level ?? 1,
                'points' => $user->points ?? 0,
                'experience' => $user->experience ?? 0,
                'streak_days' => $user->streak_days ?? 0,
                'achievements' => $user->achievements ?? [],
            ];
        }

        return $context;
    }

    /**
     * Build system prompt with context
     */
    protected function buildSystemPrompt(array $context, string $language = 'en'): string
    {
        $prompt = "You are Q2L's friendly AI coach for teachers. Prefer using the context below when it helps personalize the response, but you may also rely on your broader teaching knowledge and best practices to answer questions about lessons, quests, or motivation.\n\n";
        
        $prompt .= "SYSTEM CONTEXT:\n";
        
        if ($context['user']) {
            $prompt .= "- User: {$context['user']['name']} ({$context['user']['email']})\n";
            $prompt .= "- Role: {$context['user']['role']}\n";
        }

        if ($context['profile']) {
            $prompt .= "- Profile Information:\n";
            foreach ($context['profile'] as $key => $value) {
                if ($value) {
                    $prompt .= "  * " . ucfirst(str_replace('_', ' ', $key)) . ": {$value}\n";
                }
            }
        }

        if ($context['gamification']) {
            $prompt .= "- Progress:\n";
            $prompt .= "  * Level: {$context['gamification']['level']}\n";
            $prompt .= "  * Points: {$context['gamification']['points']}\n";
            $prompt .= "  * Experience: {$context['gamification']['experience']} XP\n";
            $prompt .= "  * Daily Streak: {$context['gamification']['streak_days']} days\n";
            if (!empty($context['gamification']['achievements'])) {
                $prompt .= "  * Achievements: " . implode(', ', $context['gamification']['achievements']) . "\n";
            }
        }

        // Language mapping
        $languageNames = [
            'en' => 'English',
            'fil' => 'Filipino',
            'bis' => 'Bisaya (Cebuano)',
        ];
        
        $selectedLanguage = $languageNames[$language] ?? 'English';

        $prompt .= "\nIMPORTANT GUIDELINES:\n";
        $prompt .= "1. Use the system context when it adds helpful personalization, but feel free to answer using general teaching knowledge.\n";
        $prompt .= "2. Give clear, actionable suggestions (lesson ideas, activities, motivation tips, etc.).\n";
        $prompt .= "3. Stay encouraging, collaborative, and practical.\n";
        $prompt .= "4. If a request is unclear or lacks details, ask a short follow-up question.\n";
        $prompt .= "5. If you truly do not know something, say so briefly and suggest an alternative.\n";
        $prompt .= "6. CRITICAL: You MUST respond in {$selectedLanguage}. All your responses should be in {$selectedLanguage}.\n";

        return $prompt;
    }
}


