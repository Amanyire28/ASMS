import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// ── Per-subject "Out Of" totals store (used by multi-subject mark sheet) ──
Alpine.store('marksTotals', {
    totals: {},
    getTotal: function(id) { return parseFloat(this.totals[String(id)]) || 100; }
});

// ── Marks entry selector component ──────────────────────────
Alpine.data('marksEntrySelector', function() {
    return {
        map: {},
        classId: '',
        subjectId: '',
        subjects: [],
        hint: '\u2014 Select Class First \u2014',
        init: function() {
            var el = document.getElementById('subjectsMapData');
            try { this.map = el ? JSON.parse(el.textContent) : {}; } catch(e) { this.map = {}; }
            this.classId   = this.$el.dataset.initClass   || '';
            this.subjectId = this.$el.dataset.initSubject || '';
            if (this.classId) this.onClassChange(true);
        },
        onClassChange: function(keepSubject) {
            if (!this.classId) {
                this.subjects  = [];
                this.hint      = '\u2014 Select Class First \u2014';
                this.subjectId = '';
                return;
            }
            var s         = this.map[this.classId] || [];
            this.subjects = s;
            this.hint     = s.length ? '\u2014 Select Subject \u2014' : 'No subjects assigned to this class';
            if (!keepSubject || !s.find(function(x) { return String(x.id) === String(this.subjectId); }, this)) {
                this.subjectId = s.length === 1 ? String(s[0].id) : '';
            }
        }
    };
});

Alpine.start();
