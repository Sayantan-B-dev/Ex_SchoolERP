// Password show/hide toggles
document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('password-toggle')) {
        const targetSel = e.target.getAttribute('data-target');
        const input = document.querySelector(targetSel);
        if (input) {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            e.target.textContent = type === 'password' ? 'Show' : 'Hide';
        }
    }
});

// Course/Department dropdown helpers
const courseOptions = [
    'BCA', 'BSc CS', 'Diploma CS', 'MBA', 'BBA', 'HR', 'Finance', 'Marketing', 'Diploma Civil', 'Diploma Mechanical', 'Diploma Electrical', 'LLB', 'BA LLB'
];


function populateSelect(selectEl, options, selected) {
    if (!selectEl) return;
    const placeholder = document.createElement('option');
    placeholder.value = '';
    placeholder.disabled = true;
    if (!selected) placeholder.selected = true;
    placeholder.textContent = 'Choose...';
    selectEl.innerHTML = '';
    selectEl.appendChild(placeholder);
    options.forEach(function(item) {
        const opt = document.createElement('option');
        opt.value = item;
        opt.textContent = item;
        if (selected && selected === item) opt.selected = true;
        selectEl.appendChild(opt);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Theme init
    const savedTheme = localStorage.getItem('sim_theme');
    if (savedTheme === 'dark' || savedTheme === 'light') {
        document.documentElement.setAttribute('data-theme', savedTheme);
    }

    // Populate course/department selects (register/profile)
    const courseSelect = document.getElementById('course');
    if (courseSelect) {
        const selectedCourse = courseSelect.getAttribute('data-selected') || '';
        populateSelect(courseSelect, courseOptions, selectedCourse);
    }

    const deptSelect = document.getElementById('department');
    if (deptSelect) {
        const selectedDept = deptSelect.getAttribute('data-selected') || '';
        populateSelect(deptSelect, departmentOptions, selectedDept);
    }
});

// Theme toggle handler
function setTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('sim_theme', theme);
}

document.addEventListener('click', function(e) {
    const darkBtn = e.target.closest('[data-theme-select="dark"]');
    const lightBtn = e.target.closest('[data-theme-select="light"]');
    if (darkBtn) {
        setTheme('dark');
    } else if (lightBtn) {
        setTheme('light');
    }
});


