/* =====================================================
   ФАЙЛ: script.js
   НАЗНАЧЕНИЕ: Интерактивность сайта
   ВКЛЮЧАЕТ: переключение темы, мобильное меню,
             валидацию форм, анимации
   ===================================================== */

// ============ ПЕРЕКЛЮЧЕНИЕ ТЕМЫ (СВЕТЛАЯ/ТЕМНАЯ) ============
const themeToggle = document.getElementById('themeToggle');
const html = document.documentElement;

// Загружаем сохраненную тему из localStorage
const savedTheme = localStorage.getItem('theme') || 'light';
html.setAttribute('data-theme', savedTheme);
updateThemeIcon(savedTheme);

if (themeToggle) {
    themeToggle.addEventListener('click', () => {
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(newTheme);
    });
}

function updateThemeIcon(theme) {
    if (!themeToggle) return;
    const icon = themeToggle.querySelector('i');
    if (icon) {
        icon.className = theme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
    }
}

// ============ МОБИЛЬНОЕ МЕНЮ ============
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const navMenu = document.querySelector('.nav-menu');

if (mobileMenuBtn && navMenu) {
    mobileMenuBtn.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        const icon = mobileMenuBtn.querySelector('i');
        icon.className = navMenu.classList.contains('active') 
            ? 'fas fa-times' 
            : 'fas fa-bars';
    });
}

// ============ КНОПКА "В ИЗБРАННОЕ" ============
document.querySelectorAll('.favorite-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        btn.classList.toggle('active');
        const icon = btn.querySelector('i');
        if (btn.classList.contains('active')) {
            icon.classList.remove('far');
            icon.classList.add('fas');
        } else {
            icon.classList.remove('fas');
            icon.classList.add('far');
        }
    });
});

// ============ ВАЛИДАЦИЯ ДАТ БРОНИРОВАНИЯ ============
const checkInInput = document.querySelector('input[name="check_in"]');
const checkOutInput = document.querySelector('input[name="check_out"]');

if (checkInInput && checkOutInput) {
    checkInInput.addEventListener('change', () => {
        const checkInDate = new Date(checkInInput.value);
        const nextDay = new Date(checkInDate);
        nextDay.setDate(nextDay.getDate() + 1);
        
        const minCheckOut = nextDay.toISOString().split('T')[0];
        checkOutInput.min = minCheckOut;
        
        // Если выбранная дата выезда раньше — обновляем
        if (checkOutInput.value && checkOutInput.value <= checkInInput.value) {
            checkOutInput.value = minCheckOut;
        }
        
        // Подсчитываем итоговую цену (на странице бронирования)
        updateBookingPrice();
    });
    
    checkOutInput.addEventListener('change', updateBookingPrice);
}

// ============ РАСЧЕТ ЦЕНЫ БРОНИРОВАНИЯ ============
function updateBookingPrice() {
    const checkIn = document.querySelector('input[name="check_in"]');
    const checkOut = document.querySelector('input[name="check_out"]');
    const priceEl = document.getElementById('pricePerNight');
    
    if (!checkIn || !checkOut || !priceEl) return;
    if (!checkIn.value || !checkOut.value) return;
    
    const inDate = new Date(checkIn.value);
    const outDate = new Date(checkOut.value);
    const nights = Math.ceil((outDate - inDate) / (1000 * 60 * 60 * 24));
    
    if (nights <= 0) return;
    
    const pricePerNight = parseFloat(priceEl.dataset.price);
    const subtotal = pricePerNight * nights;
    const serviceFee = subtotal * 0.05; // 5% сервисный сбор
    const total = subtotal + serviceFee;
    
    document.getElementById('nightsCount').textContent = nights;
    document.getElementById('subtotal').textContent = formatPrice(subtotal);
    document.getElementById('serviceFee').textContent = formatPrice(serviceFee);
    document.getElementById('totalPrice').textContent = formatPrice(total);
    
    // Передаем итоговую цену в скрытое поле формы
    const totalInput = document.getElementById('totalPriceInput');
    if (totalInput) totalInput.value = total;
}

function formatPrice(price) {
    return new Intl.NumberFormat('ru-RU').format(Math.round(price)) + ' ₸';
}

// Запускаем при загрузке страницы (для страницы бронирования)
document.addEventListener('DOMContentLoaded', updateBookingPrice);

// ============ АНИМАЦИЯ ПОЯВЛЕНИЯ ЭЛЕМЕНТОВ ПРИ СКРОЛЛЕ ============
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

document.querySelectorAll('.hotel-card, .city-card, .feature-card').forEach((el, index) => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = `opacity 0.6s ease ${index * 0.05}s, transform 0.6s ease ${index * 0.05}s`;
    observer.observe(el);
});

// ============ ВАЛИДАЦИЯ ФОРМ ============
const forms = document.querySelectorAll('form[data-validate]');

forms.forEach(form => {
    form.addEventListener('submit', (e) => {
        let isValid = true;
        
        // Проверка email
        const email = form.querySelector('input[type="email"]');
        if (email && email.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value)) {
                showError(email, 'Введите корректный email');
                isValid = false;
            }
        }
        
        // Проверка пароля
        const password = form.querySelector('input[name="password"]');
        if (password && password.value && password.value.length < 6) {
            showError(password, 'Пароль должен быть не менее 6 символов');
            isValid = false;
        }
        
        // Проверка совпадения паролей
        const passwordConfirm = form.querySelector('input[name="password_confirm"]');
        if (passwordConfirm && password && password.value !== passwordConfirm.value) {
            showError(passwordConfirm, 'Пароли не совпадают');
            isValid = false;
        }
        
        if (!isValid) e.preventDefault();
    });
});

function showError(input, message) {
    input.style.borderColor = '#ef4444';
    
    // Удаляем старое сообщение об ошибке
    const oldError = input.parentElement.querySelector('.field-error');
    if (oldError) oldError.remove();
    
    const errorEl = document.createElement('span');
    errorEl.className = 'field-error';
    errorEl.style.cssText = 'color: #ef4444; font-size: 12px; margin-top: 4px; display: block;';
    errorEl.textContent = message;
    input.parentElement.appendChild(errorEl);
    
    // Убираем ошибку при изменении
    input.addEventListener('input', () => {
        input.style.borderColor = '';
        errorEl.remove();
    }, { once: true });
}

// ============ ПЛАВНАЯ ПРОКРУТКА ============
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href === '#') return;
        
        const target = document.querySelector(href);
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// ============ АВТО-ЗАКРЫТИЕ АЛЕРТОВ ============
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.transition = 'opacity 0.5s, transform 0.5s';
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-10px)';
        setTimeout(() => alert.remove(), 500);
    }, 5000);
});
