const mobileMenuBtn = document.getElementById('mobileMenuBtn');
if (mobileMenuBtn) {
    mobileMenuBtn.addEventListener('click', function () {
        document.getElementById('mobileMenu').classList.toggle('hidden');
    });
}

function changeLanguage(lang) {
    localStorage.setItem('selectedLanguage', lang);

    const translations = {
        'en': {
            'nav-home': 'Home',
            'nav-quest': 'Quest Path',
            'nav-missions': 'Live Missions',
            'nav-rewards': 'Rewards',
            'nav-community': 'Community',
            'btn-login': 'Log In',
            'hero-badge': 'AI-Powered Learning',
            'hero-title-1': 'Quest2Learn',
            'hero-title-2': 'AI Gamified Tutoring',
            'hero-title-3': 'for Indigenous People',
            'hero-subtitle': 'Experience personalized AI tutoring through gamified learning adventures. Master reading, writing, and language skills with intelligent feedback designed specifically for indigenous learners.',
            'hero-cta-primary': 'Start Learning Now',
            'hero-cta-secondary': 'Explore Features',
            'hero-stat-quests-label': 'Active Students',
            'hero-stat-xp-label': 'Powered Tutoring',
            'hero-stat-speed-label': 'Gamified Learning',
            'quest-title': 'How Quest2Learn Works',
            'quest-subtitle': 'An intelligent tutoring system that adapts to each indigenous learner\'s unique needs through gamified experiences.',
            'quest-step-1-title': 'Assessment',
            'quest-step-1-desc': 'AI evaluates your current skills and creates a personalized learning path.',
            'quest-step-2-title': 'Interactive Tutoring',
            'quest-step-2-desc': 'Learn through gamified lessons with real-time AI feedback and guidance.',
            'quest-step-3-title': 'Practice & Mastery',
            'quest-step-3-desc': 'Engage in fun challenges that reinforce learning with instant corrections.',
            'quest-step-4-title': 'Progress Tracking',
            'quest-step-4-desc': 'Monitor your growth with detailed analytics and achievement milestones.',
            'missions-title': 'AI Tutoring Features',
            'missions-subtitle': 'Experience cutting-edge AI technology designed to support indigenous learners in their educational journey.',
            'mission-card-1-title': 'AI-Powered Feedback',
            'mission-card-1-desc': 'Receive instant, personalized feedback on pronunciation, grammar, and comprehension from our intelligent AI tutor.',
            'mission-card-2-title': 'Gamified Learning',
            'mission-card-2-desc': 'Learn through engaging games, quests, and challenges that make education fun and motivating for indigenous students.',
            'mission-card-3-title': 'Cultural Adaptation',
            'mission-card-3-desc': 'Content and teaching methods specifically tailored to respect and incorporate indigenous cultural contexts and languages.',
            'rewards-title': 'Key Features & Benefits',
            'rewards-subtitle': 'Discover what makes Quest2Learn the perfect tutoring system for indigenous learners.',
            'reward-1-title': 'Intelligent Tutoring',
            'reward-1-desc': 'Advanced AI algorithms provide personalized instruction that adapts to each learner\'s unique pace and learning style.',
            'reward-2-title': 'Gamification',
            'reward-2-desc': 'Points, levels, achievements, and leaderboards transform learning into an engaging, motivating experience.',
            'reward-3-title': 'Indigenous Focus',
            'reward-3-desc': 'Culturally-responsive content and multilingual support for indigenous languages including Filipino and Bisaya.',
            'community-title': 'Why Quest2Learn?',
            'community-subtitle': 'Empowering indigenous communities through accessible, culturally-aware AI-powered education.',
            'community-quote': '"Quest2Learn has revolutionized how we teach indigenous students. The AI tutor provides personalized support that respects our cultural values while making learning engaging and fun."',
            'community-quote-author': 'Maria Santos',
            'community-quote-role': 'Indigenous Education Coordinator',
            'community-stat-1-label': 'Student Engagement Rate',
            'community-stat-2-label': 'Faster Learning Progress',
            'cta-title': 'Ready to Start Your Learning Journey?',
            'cta-subtitle': 'Join Quest2Learn today and experience AI-powered gamified tutoring designed specifically for indigenous learners.',
            'btn-get-started': 'Start Learning Now',
            'btn-teacher': 'For Educators'
        },
        'fil': {
            'nav-home': 'Tahanan',
            'nav-quest': 'Landas ng Quest',
            'nav-missions': 'Live Missions',
            'nav-rewards': 'Mga Gantimpala',
            'nav-community': 'Komunidad',
            'btn-login': 'Mag-login',
            'hero-badge': 'AI-Powered Learning',
            'hero-title-1': 'Quest2Learn',
            'hero-title-2': 'AI Gamified Tutoring',
            'hero-title-3': 'para sa mga Katutubo',
            'hero-subtitle': 'Makaranas ng personalisadong AI tutoring sa pamamagitan ng gamified learning adventures. Masterin ang pagbasa, pagsulat, at kasanayan sa wika gamit ang matalinong feedback na idinisenyo para sa mga katutubong mag-aaral.',
            'hero-cta-primary': 'Magsimula Ngayon',
            'hero-cta-secondary': 'Tingnan ang Features',
            'hero-stat-quests-label': 'Aktibong Mag-aaral',
            'hero-stat-xp-label': 'AI Tutoring',
            'hero-stat-speed-label': 'Gamified Learning',
            'quest-title': 'Paano Gumagana ang Quest2Learn',
            'quest-subtitle': 'Isang matalinong sistema ng pagtuturo na umaangkop sa natatanging pangangailangan ng bawat katutubong mag-aaral sa pamamagitan ng gamified experiences.',
            'quest-step-1-title': 'Assessment',
            'quest-step-1-desc': 'Sinusuri ng AI ang iyong kasalukuyang kasanayan at lumilikha ng personalisadong learning path.',
            'quest-step-2-title': 'Interactive Tutoring',
            'quest-step-2-desc': 'Matuto sa pamamagitan ng gamified lessons na may real-time AI feedback at gabay.',
            'quest-step-3-title': 'Practice & Mastery',
            'quest-step-3-desc': 'Makilahok sa masayang hamon na nagpapatibay sa pagkatuto na may instant corrections.',
            'quest-step-4-title': 'Progress Tracking',
            'quest-step-4-desc': 'Subaybayan ang iyong paglago gamit ang detalyadong analytics at achievement milestones.',
            'missions-title': 'AI Tutoring Features',
            'missions-subtitle': 'Makaranas ng cutting-edge AI technology na idinisenyo upang suportahan ang mga katutubong mag-aaral sa kanilang educational journey.',
            'mission-card-1-title': 'AI-Powered Feedback',
            'mission-card-1-desc': 'Tumanggap ng instant, personalisadong feedback sa pagbigkas, grammar, at comprehension mula sa aming matalinong AI tutor.',
            'mission-card-2-title': 'Gamified Learning',
            'mission-card-2-desc': 'Matuto sa pamamagitan ng nakakaengganyong laro, quest, at hamon na ginagawang masaya at nakakamotibo ang edukasyon para sa mga katutubong mag-aaral.',
            'mission-card-3-title': 'Cultural Adaptation',
            'mission-card-3-desc': 'Nilalaman at pamamaraan ng pagtuturo na partikular na idinisenyo upang igalang at isama ang mga indigenous cultural contexts at wika.',
            'rewards-title': 'Key Features & Benefits',
            'rewards-subtitle': 'Alamin kung ano ang ginagawang perpekto ng Quest2Learn na tutoring system para sa mga katutubong mag-aaral.',
            'reward-1-title': 'Intelligent Tutoring',
            'reward-1-desc': 'Advanced AI algorithms na nagbibigay ng personalisadong instruksyon na umaangkop sa natatanging bilis at learning style ng bawat mag-aaral.',
            'reward-2-title': 'Gamification',
            'reward-2-desc': 'Points, levels, achievements, at leaderboards na nagpapalit sa pagkatuto sa isang nakakaengganyo at nakakamotibong karanasan.',
            'reward-3-title': 'Indigenous Focus',
            'reward-3-desc': 'Culturally-responsive na nilalaman at multilingual support para sa mga indigenous languages kabilang ang Filipino at Bisaya.',
            'community-title': 'Bakit Quest2Learn?',
            'community-subtitle': 'Pagpapalakas sa mga indigenous communities sa pamamagitan ng accessible, culturally-aware AI-powered education.',
            'community-quote': '"Binago ng Quest2Learn kung paano namin tinuturuan ang mga katutubong mag-aaral. Ang AI tutor ay nagbibigay ng personalisadong suporta na iginagalang ang aming cultural values habang ginagawang masaya at nakakaengganyo ang pagkatuto."',
            'community-quote-author': 'Maria Santos',
            'community-quote-role': 'Indigenous Education Coordinator',
            'community-stat-1-label': 'Student Engagement Rate',
            'community-stat-2-label': 'Mas Mabilis na Learning Progress',
            'cta-title': 'Handa Ka Na Bang Magsimula?',
            'cta-subtitle': 'Sumali sa Quest2Learn ngayon at makaranas ng AI-powered gamified tutoring na idinisenyo para sa mga katutubong mag-aaral.',
            'btn-get-started': 'Magsimula Ngayon',
            'btn-teacher': 'Para sa mga Guro'
        },
        'bis': {
            'nav-home': 'Balay',
            'nav-quest': 'Landas sa Quest',
            'nav-missions': 'Mga Live Mission',
            'nav-rewards': 'Mga Ganti',
            'nav-community': 'Komunidad',
            'btn-login': 'Mag-login',
            'hero-badge': 'AI-Powered Learning',
            'hero-title-1': 'Quest2Learn',
            'hero-title-2': 'AI Gamified Tutoring',
            'hero-title-3': 'para sa Lumad',
            'hero-subtitle': 'Makasinati ug personalisadong AI tutoring pinaagi sa gamified learning adventures. Masterin ang pagbasa, pagsulat, ug kahanas sa pinulongan gamit ang maalamong feedback nga gidisenyo alang sa lumad nga magtutungha.',
            'hero-cta-primary': 'Sugdi Karon',
            'hero-cta-secondary': 'Tan-awa ang Features',
            'hero-stat-quests-label': 'Aktibong Magtutungha',
            'hero-stat-xp-label': 'AI Tutoring',
            'hero-stat-speed-label': 'Gamified Learning',
            'quest-title': 'Giunsa Magtrabaho ang Quest2Learn',
            'quest-subtitle': 'Usa ka maalamong sistema sa pagtudlo nga nag-adjust sa talagsaon nga panginahanglan sa matag lumad nga magtutungha pinaagi sa gamified experiences.',
            'quest-step-1-title': 'Assessment',
            'quest-step-1-desc': 'Ang AI nag-evaluate sa imong kasamtangang kahanas ug naghimo ug personalisadong learning path.',
            'quest-step-2-title': 'Interactive Tutoring',
            'quest-step-2-desc': 'Matuto pinaagi sa gamified lessons nga adunay real-time AI feedback ug giya.',
            'quest-step-3-title': 'Practice & Mastery',
            'quest-step-3-desc': 'Makig-uban sa makalingaw nga mga hagit nga nagpalig-on sa pagkat-on nga adunay instant corrections.',
            'quest-step-4-title': 'Progress Tracking',
            'quest-step-4-desc': 'Subaybayan ang imong pagtubo gamit ang detalyadong analytics ug achievement milestones.',
            'missions-title': 'AI Tutoring Features',
            'missions-subtitle': 'Makasinati ug cutting-edge AI technology nga gidisenyo aron suportahan ang lumad nga magtutungha sa ilang educational journey.',
            'mission-card-1-title': 'AI-Powered Feedback',
            'mission-card-1-desc': 'Modawat ug instant, personalisadong feedback sa paglitok, grammar, ug pagsabot gikan sa among maalamong AI tutor.',
            'mission-card-2-title': 'Gamified Learning',
            'mission-card-2-desc': 'Matuto pinaagi sa makalingaw nga mga dula, quest, ug hagit nga naghimo sa edukasyon nga makalingaw ug makamotibo para sa lumad nga magtutungha.',
            'mission-card-3-title': 'Cultural Adaptation',
            'mission-card-3-desc': 'Kontento ug pamaagi sa pagtudlo nga partikular nga gidisenyo aron tahuron ug isama ang lumad nga cultural contexts ug pinulongan.',
            'rewards-title': 'Key Features & Benefits',
            'rewards-subtitle': 'Diskobre kung unsa ang naghimo sa Quest2Learn nga perpektong tutoring system para sa lumad nga magtutungha.',
            'reward-1-title': 'Intelligent Tutoring',
            'reward-1-desc': 'Advanced AI algorithms nga naghatag ug personalisadong instruksyon nga nag-adjust sa talagsaong bilis ug learning style sa matag magtutungha.',
            'reward-2-title': 'Gamification',
            'reward-2-desc': 'Points, levels, achievements, ug leaderboards nga nagbag-o sa pagkat-on ngadto sa makalingaw ug makamotibong karanasan.',
            'reward-3-title': 'Indigenous Focus',
            'reward-3-desc': 'Culturally-responsive nga kontento ug multilingual support para sa lumad nga pinulongan lakip ang Filipino ug Bisaya.',
            'community-title': 'Ngano Quest2Learn?',
            'community-subtitle': 'Pagpalig-on sa lumad nga komunidad pinaagi sa accessible, culturally-aware AI-powered education.',
            'community-quote': '"Giusab sa Quest2Learn kung giunsa namo pagtudlo ang lumad nga magtutungha. Ang AI tutor naghatag ug personalisadong suporta nga nagtahod sa among cultural values samtang naghimo sa pagkat-on nga makalingaw ug makalingaw."',
            'community-quote-author': 'Maria Santos',
            'community-quote-role': 'Indigenous Education Coordinator',
            'community-stat-1-label': 'Student Engagement Rate',
            'community-stat-2-label': 'Mas Paspas nga Learning Progress',
            'cta-title': 'Andam Ka Na Ba Magsugod?',
            'cta-subtitle': 'Apil sa Quest2Learn karon ug makasinati ug AI-powered gamified tutoring nga gidisenyo para sa lumad nga magtutungha.',
            'btn-get-started': 'Sugdi Karon',
            'btn-teacher': 'Para sa mga Magtutudlo'
        }
    };

    const langData = translations[lang] || translations['en'];

    document.querySelectorAll('[data-translate]').forEach(el => {
        const key = el.getAttribute('data-translate');
        if (langData[key]) {
            el.textContent = langData[key];
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const savedLang = localStorage.getItem('selectedLanguage') || 'en';
    changeLanguage(savedLang);
    const langText = savedLang === 'fil' ? 'Filipino' : savedLang === 'bis' ? 'Bisaya' : 'English';
    const desktopLabel = document.getElementById('currentLang');
    if (desktopLabel) {
        desktopLabel.textContent = langText;
    }
    const mobileLabel = document.getElementById('currentLangMobile');
    if (mobileLabel) {
        mobileLabel.textContent = langText;
    }
});
