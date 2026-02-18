<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PDF Library · Quest2Learn</title>
    <x-theme-script />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --brand-primary: #4f46e5;
            --brand-primary-dark: #4338ca;
            --page-bg: linear-gradient(140deg, #e8edff 0%, #f4f7ff 100%);
            --card-bg: #ffffff;
            --surface-border: rgba(148, 163, 184, 0.35);
            --text-primary: #1e293b;
            --text-muted: #64748b;
        }

        .dark {
            color-scheme: dark;
            --brand-primary: #6366f1;
            --brand-primary-dark: #818cf8;
            --page-bg: linear-gradient(160deg, #0f172a 0%, #1e293b 50%, #020617 100%);
            --card-bg: rgba(15, 23, 42, 0.88);
            --surface-border: rgba(71, 85, 105, 0.55);
            --text-primary: #e2e8f0;
            --text-muted: #cbd5e5;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--page-bg);
            color: var(--text-primary);
            min-height: 100vh;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--surface-border);
            border-radius: 1.5rem;
            box-shadow: 0 24px 55px -25px rgba(15, 23, 42, 0.25);
            transition: transform 180ms ease, box-shadow 180ms ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 30px 60px -30px rgba(79, 70, 229, 0.35);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-primary-dark) 100%);
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 180ms ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 28px -12px rgba(99, 102, 241, 0.8);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.92);
            color: var(--brand-primary);
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            border: 1px solid var(--surface-border);
            cursor: pointer;
            transition: all 180ms ease;
        }

        .btn-secondary:hover {
            background: var(--surface-border);
        }

        .pdf-item {
            background: var(--card-bg);
            border: 1px solid var(--surface-border);
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 200ms ease;
        }

        .pdf-item:hover {
            border-color: var(--brand-primary);
            box-shadow: 0 8px 25px -12px rgba(79, 70, 229, 0.25);
        }

        .upload-zone {
            border: 2px dashed var(--surface-border);
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            transition: all 200ms ease;
            cursor: pointer;
        }

        .upload-zone:hover {
            border-color: var(--brand-primary);
            background: rgba(79, 70, 229, 0.05);
        }

        .upload-zone.dragover {
            border-color: var(--brand-primary);
            background: rgba(79, 70, 229, 0.1);
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Navigation -->
    <nav class="sticky top-0 z-50 backdrop-blur-lg bg-[color:var(--card-bg)]/90 border-b border-[color:var(--surface-border)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('teacher.dashboard') }}" class="flex items-center space-x-3">
                        <img src="{{ asset('assets/logo.png') }}" alt="Q2L Logo" class="h-8 w-auto rounded-lg">
                        <div>
                            <p class="text-xs font-semibold text-[color:var(--text-muted)] uppercase tracking-wider">Quest2Learn</p>
                            <h1 class="text-lg font-bold text-[color:var(--text-primary)]">PDF Library</h1>
                        </div>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <x-translation-toggle />
                    <x-theme-toggle />
                    <a href="{{ route('teacher.dashboard') }}" class="btn-secondary">
                        ← Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="pdfLibrary()">
        <!-- Header with Upload Button -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-[color:var(--text-primary)] mb-2">PDF Library</h2>
                    <p class="text-[color:var(--text-muted)]">Upload and manage PDF reading materials for your students</p>
                </div>
                <button @click="showUploadModal = true" class="btn-primary">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Upload PDF
                </button>
            </div>
        </div>

        <!-- PDF Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <template x-for="pdf in pdfs" :key="pdf.id">
                <div class="pdf-item">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-[color:var(--text-primary)]" x-text="pdf.title"></h3>
                                <p class="text-sm text-[color:var(--text-muted)]" x-text="pdf.classroom?.name || 'General'"></p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a :href="pdf.file_url" target="_blank" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                            <button @click="openAnalytics(pdf.id)" class="text-emerald-600 hover:text-emerald-800" title="View Reading Analytics">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6m6 0h6m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2"></path>
                                </svg>
                            </button>
                            <button @click="deletePdf(pdf.id)" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <p class="text-sm text-[color:var(--text-muted)] mb-3" x-text="pdf.description || 'No description provided'"></p>
                    
                    <div class="flex items-center justify-between text-xs text-[color:var(--text-muted)]">
                        <span x-text="formatFileSize(pdf.file_size)"></span>
                        <span x-text="formatDate(pdf.created_at)"></span>
                    </div>
                    
                    <div class="mt-3 flex items-center space-x-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" 
                              :class="pdf.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                              x-text="pdf.is_active ? 'Active' : 'Inactive'"></span>
                        <template x-if="pdf.metadata?.page_count">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800" 
                                  x-text="pdf.metadata.page_count + ' pages'"></span>
                        </template>
                        <template x-if="pdfAnalytics[pdf.id] && pdfAnalytics[pdf.id].avg_accuracy !== null">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800" 
                                  x-text="'Avg: ' + pdfAnalytics[pdf.id].avg_accuracy + '%'"></span>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- Analytics Modal -->
        <div x-show="showAnalyticsModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="closeAnalytics()"></div>

                <div class="relative bg-white rounded-xl max-w-4xl w-full p-6">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Reading Analytics</h3>
                            <p class="text-sm text-gray-600" x-text="analyticsTitle"></p>
                        </div>
                        <button type="button" @click="closeAnalytics()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <template x-if="activeAnalytics">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="p-4 rounded-lg border border-gray-200">
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Average Score</p>
                                    <p class="mt-1 text-2xl font-bold text-gray-900" x-text="formatPercent(activeAnalytics.avg_accuracy)"></p>
                                </div>
                                <div class="p-4 rounded-lg border border-gray-200">
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Students Attempted</p>
                                    <p class="mt-1 text-2xl font-bold text-gray-900" x-text="activeAnalytics.student_count"></p>
                                </div>
                                <div class="p-4 rounded-lg border border-gray-200">
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Sessions</p>
                                    <p class="mt-1 text-2xl font-bold text-gray-900" x-text="activeAnalytics.attempt_count"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-semibold text-gray-900">Top Difficult Words</h4>
                                        <p class="text-xs text-gray-500">(most frequent)</p>
                                    </div>
                                    <div class="border border-gray-200 rounded-lg p-3">
                                        <template x-if="(activeAnalytics.top_words || []).length === 0">
                                            <p class="text-sm text-gray-600">No difficult words recorded yet.</p>
                                        </template>
                                        <div class="flex flex-wrap gap-2" x-show="(activeAnalytics.top_words || []).length > 0">
                                            <template x-for="item in activeAnalytics.top_words" :key="item.word">
                                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                    <span x-text="item.word"></span>
                                                    <span class="px-2 py-0.5 rounded-full bg-yellow-200" x-text="item.count"></span>
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-semibold text-gray-900">Latest Student Scores</h4>
                                        <p class="text-xs text-gray-500">(per student)</p>
                                    </div>
                                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                                        <div class="max-h-72 overflow-y-auto">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50 sticky top-0">
                                                    <tr>
                                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Student</th>
                                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Score</th>
                                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Difficult</th>
                                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Attempts</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    <template x-for="row in (activeAnalytics.students || [])" :key="row.student_id">
                                                        <tr>
                                                            <td class="px-3 py-2 text-sm text-gray-900" x-text="row.student_name"></td>
                                                            <td class="px-3 py-2 text-sm font-semibold" :class="row.accuracy !== null && row.accuracy >= 85 ? 'text-emerald-700' : row.accuracy !== null && row.accuracy >= 70 ? 'text-yellow-700' : 'text-red-700'" x-text="formatPercent(row.accuracy)"></td>
                                                            <td class="px-3 py-2 text-sm text-gray-700" x-text="row.difficult_words_count"></td>
                                                            <td class="px-3 py-2 text-sm text-gray-700" x-text="row.attempts"></td>
                                                        </tr>
                                                    </template>
                                                    <template x-if="(activeAnalytics.students || []).length === 0">
                                                        <tr>
                                                            <td colspan="4" class="px-3 py-4 text-sm text-gray-600">No student sessions recorded yet.</td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="!activeAnalytics">
                        <p class="text-sm text-gray-600">No analytics available for this PDF yet.</p>
                    </template>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div x-show="pdfs.length === 0" class="text-center py-12">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-[color:var(--text-primary)] mb-2">No PDFs uploaded yet</h3>
            <p class="text-[color:var(--text-muted)] mb-4">Upload your first PDF to get started with reading materials for your students.</p>
            <button @click="showUploadModal = true" class="btn-primary">Upload PDF</button>
        </div>

        <!-- Upload Modal -->
        <div x-show="showUploadModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showUploadModal = false"></div>
                
                <div class="relative bg-white rounded-xl max-w-lg w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Upload PDF</h3>
                    
                    <form @submit.prevent="uploadPdf">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                                <input type="text" x-model="uploadForm.title" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea x-model="uploadForm.description" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Classroom (Optional)</label>
                                <select x-model="uploadForm.classroom_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">General (Available to all classes)</option>
                                    <template x-for="cls in classes" :key="cls.id">
                                        <option :value="cls.id" x-text="cls.name"></option>
                                    </template>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">PDF File *</label>
                                <div class="upload-zone" 
                                     @dragover.prevent="dragover = true"
                                     @dragleave.prevent="dragover = false"
                                     @drop.prevent="handleFileDrop"
                                     :class="{ 'dragover': dragover }"
                                     @click="$refs.fileInput.click()">
                                    <input type="file" x-ref="fileInput" @change="handleFileSelect" accept=".pdf" class="hidden" required>
                                    <div x-show="!uploadForm.file">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-600">Click to upload or drag and drop</p>
                                        <p class="text-xs text-gray-500">PDF files up to 10MB</p>
                                    </div>
                                    <div x-show="uploadForm.file">
                                        <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="mt-2 text-sm font-medium text-gray-900" x-text="uploadForm.file.name"></p>
                                        <p class="text-xs text-gray-500" x-text="formatFileSize(uploadForm.file.size)"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" @click="showUploadModal = false" 
                                    class="btn-secondary">Cancel</button>
                            <button type="submit" :disabled="uploading"
                                    class="btn-primary" x-text="uploading ? 'Uploading...' : 'Upload PDF'"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        function pdfLibrary() {
            return {
                pdfs: @json($pdfs),
                classes: @json($classes),
                pdfAnalytics: @json($pdfAnalytics ?? []),
                showAnalyticsModal: false,
                activeAnalytics: null,
                analyticsTitle: '',
                showUploadModal: false,
                uploading: false,
                dragover: false,
                uploadForm: {
                    title: '',
                    description: '',
                    classroom_id: '',
                    file: null
                },

                openAnalytics(pdfId) {
                    const pdf = this.pdfs.find(p => p.id === pdfId);
                    this.analyticsTitle = pdf ? (pdf.title + ' • ' + (pdf.classroom?.name || 'General')) : '';
                    this.activeAnalytics = this.pdfAnalytics[pdfId] || null;
                    this.showAnalyticsModal = true;
                },

                closeAnalytics() {
                    this.showAnalyticsModal = false;
                    this.activeAnalytics = null;
                    this.analyticsTitle = '';
                },

                formatPercent(value) {
                    if (value === null || value === undefined || value === '') return '-';
                    const num = Number(value);
                    if (Number.isNaN(num)) return '-';
                    return num.toFixed(0) + '%';
                },
                
                async uploadPdf() {
                    this.uploading = true;
                    
                    const formData = new FormData();
                    formData.append('pdf_file', this.uploadForm.file);
                    formData.append('title', this.uploadForm.title);
                    formData.append('description', this.uploadForm.description);
                    formData.append('classroom_id', this.uploadForm.classroom_id);
                    
                    try {
                        const response = await fetch('{{ route("teacher.pdf.upload") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: formData
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            this.pdfs.unshift(result.pdf);
                            this.showUploadModal = false;
                            this.resetUploadForm();
                            this.showNotification('PDF uploaded successfully!', 'success');
                        } else {
                            this.showNotification(result.message, 'error');
                        }
                    } catch (error) {
                        this.showNotification('Upload failed. Please try again.', 'error');
                    } finally {
                        this.uploading = false;
                    }
                },
                
                async deletePdf(pdfId) {
                    if (!confirm('Are you sure you want to delete this PDF?')) return;
                    
                    try {
                        const response = await fetch(`{{ route("teacher.pdf.delete", ":pdf") }}`.replace(':pdf', pdfId), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            }
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            this.pdfs = this.pdfs.filter(pdf => pdf.id !== pdfId);
                            this.showNotification('PDF deleted successfully!', 'success');
                        } else {
                            this.showNotification(result.message, 'error');
                        }
                    } catch (error) {
                        this.showNotification('Delete failed. Please try again.', 'error');
                    }
                },
                
                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (file && file.type === 'application/pdf') {
                        this.uploadForm.file = file;
                    }
                },
                
                handleFileDrop(event) {
                    this.dragover = false;
                    const file = event.dataTransfer.files[0];
                    if (file && file.type === 'application/pdf') {
                        this.uploadForm.file = file;
                    }
                },
                
                resetUploadForm() {
                    this.uploadForm = {
                        title: '',
                        description: '',
                        classroom_id: '',
                        file: null
                    };
                },
                
                formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                },
                
                formatDate(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                },
                
                showNotification(message, type) {
                    // Simple notification - you can replace with a better notification system
                    const notification = document.createElement('div');
                    notification.className = `fixed top-4 right-4 px-4 py-3 rounded-lg text-white z-50 ${
                        type === 'success' ? 'bg-green-500' : 'bg-red-500'
                    }`;
                    notification.textContent = message;
                    document.body.appendChild(notification);
                    
                    setTimeout(() => {
                        notification.remove();
                    }, 3000);
                }
            }
        }
    </script>
</body>
</html>
