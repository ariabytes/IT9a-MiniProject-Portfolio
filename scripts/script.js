document.querySelectorAll('.skill-checkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const checked = document.querySelectorAll('.skill-checkbox:checked');
        const btn = document.getElementById('skillDropdown');
        if (checked.length === 0) {
            btn.textContent = 'Select Skills';
        } else {
            const names = Array.from(checked).map(cb => cb.nextElementSibling.textContent.trim());
            btn.textContent = names.join(', ');
        }
    });
});

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
}, {
    threshold: 0.1
});

document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
