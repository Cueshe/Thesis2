// Student Grade Selection JavaScript
(function() {
    const LANG_KEY = 'selectedLanguage';
    const translations = {
        en: {
            'grade-portal-label': 'Student Portal',
            'grade-hero-heading': 'Almost there, {name}!',
            'grade-sign-out': 'Sign out',
            'grade-step-label': 'Step 2 of 2',
            'grade-heading': 'Choose your grade level',
            'grade-description': 'We use your grade level to tailor lessons, leaderboards, and assignments just for you. Pick the grade that matches your current class so we can set everything up correctly.',
            'grade-level-label': 'Level {grade}',
            'grade-card-title': 'Grade {grade}',
            'grade-card-description': 'Recommended content for Grade {grade} students.',
            'grade-save-button': 'Save my grade level',
            'grade-helper-text': 'Need a different grade later? You can update this anytime from your profile settings.'
        },
        fil: {
            'grade-portal-label': 'Portal ng Mag-aaral',
            'grade-hero-heading': 'Malapit na tayo, {name}!',
            'grade-sign-out': 'Lumabas',
            'grade-step-label': 'Hakbang 2 ng 2',
            'grade-heading': 'Piliin ang iyong baitang',
            'grade-description': 'Ginagamit namin ang iyong baitang upang iangkop ang mga aralin, leaderboard, at mga takdang-aralin para sa iyo. Piliin ang baitang na tumutugma sa iyong kasalukuyang klase upang maayos naming maihanda ang lahat.',
            'grade-level-label': 'Baitang {grade}',
            'grade-card-title': 'Grade {grade}',
            'grade-card-description': 'Inirerekomendang nilalaman para sa mga mag-aaral ng Baitang {grade}.',
            'grade-save-button': 'I-save ang aking baitang',
            'grade-helper-text': 'Kailangang baguhin ang baitang? Maaari mo itong i-update anumang oras sa iyong profile settings.'
        },
        bis: {
            'grade-portal-label': 'Portal sa Estudyante',
            'grade-hero-heading': 'Hapit na ta, {name}!',
            'grade-sign-out': 'Pagawas',
            'grade-step-label': 'Lakang 2 sa 2',
            'grade-heading': 'Pilia ang imong grado',
            'grade-description': 'Gigamit namo ang imong grado aron iangay ang mga leksyon, leaderboard, ug mga buluhaton para kanimo. Pilia ang grado nga nagmatch sa imong klase aron among ma-set up ug tarong.',
            'grade-level-label': 'Grado {grade}',
            'grade-card-title': 'Grade {grade}',
            'grade-card-description': 'Girekomenda nga sulod para sa mga estudyante sa Grade {grade}.',
            'grade-save-button': 'I-save ang akong grado',
            'grade-helper-text': 'Kinahanglan usbon ang grado? Mahimo nimo kini i-update bisan kanus-a sa imong profile settings.'
        }
    };

    function getSavedLanguage() {
        return localStorage.getItem(LANG_KEY) || 'en';
    }

    function setSavedLanguage(lang) {
        localStorage.setItem(LANG_KEY, lang);
    }

    function formatText(template, el) {
        if (!template) {
            return null;
        }

        let text = template;
        const name = el?.dataset?.name || '';
        const grade = el?.dataset?.grade || '';

        if (name) {
            text = text.replace('{name}', name);
        }

        if (grade) {
            text = text.replaceAll('{grade}', grade);
        }

        return text;
    }

    function applyLanguage(lang) {
        const langData = translations[lang] || translations.en;
        document.querySelectorAll('[data-translate]').forEach((el) => {
            const key = el.dataset.translate;
            if (!key) return;
            const template = langData[key];
            const text = formatText(template, el);
            if (text) {
                el.textContent = text;
            }
        });

        const langText = lang === 'fil' ? 'Filipino' : lang === 'bis' ? 'Bisaya' : 'English';
        document.querySelectorAll('.translation-current-lang').forEach(el => {
            el.textContent = langText;
        });
    }

    window.changeLanguage = function(lang) {
        setSavedLanguage(lang);
        applyLanguage(lang);
    };

    document.addEventListener('DOMContentLoaded', () => {
        applyLanguage(getSavedLanguage());
    });
})();
