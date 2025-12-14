
        // Слайдер
        const slider = document.querySelector('.slider');
        const slides = document.querySelectorAll('.slide');
        const indicators = document.querySelectorAll('.indicator');
        let currentSlide = 0;
        let slideInterval;

        function updateSlider() {
            slider.style.transform = `translateX(-${currentSlide * 33.333}%)`;
            
            // Обновляем индикаторы
            indicators.forEach((indicator, index) => {
                if (index === currentSlide) {
                    indicator.classList.add('active');
                } else {
                    indicator.classList.remove('active');
                }
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            updateSlider();
        }

        function goToSlide(index) {
            currentSlide = index;
            updateSlider();
        }

        // Автопереключение слайдов
        function startSlider() {
            slideInterval = setInterval(nextSlide, 3000);
        }

        function stopSlider() {
            clearInterval(slideInterval);
        }

        // Инициализация слайдера
        startSlider();

        // Обработчики для индикаторов
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                goToSlide(index);
                stopSlider();
                startSlider();
            });
        });

        // Остановка слайдера при наведении
        const sliderContainer = document.querySelector('.slider-container');
        sliderContainer.addEventListener('mouseenter', stopSlider);
        sliderContainer.addEventListener('mouseleave', startSlider);

        // Анимация появления элементов при прокрутке
        const fadeElements = document.querySelectorAll('.fade-in');
        
        function checkFade() {
            fadeElements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;
                
                if (elementTop < windowHeight - 100) {
                    element.classList.add('visible');
                }
            });
        }

        // Проверяем при загрузке и прокрутке
        window.addEventListener('load', checkFade);
        window.addEventListener('scroll', checkFade);

        // Плавный скролл для навигации
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Анимация кнопок "Записаться"
        const courseButtons = document.querySelectorAll('.course-footer .btn');
        courseButtons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#0d62d9';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '#1a73e8';
            });
        });

        // Загрузка логотипов
        function loadLogo() {
            // Пример загрузки логотипа (раскомментировать и указать путь)
       
            const headerLogo = document.querySelector('.logo-img');
            const footerLogo = document.querySelector('.footer-logo-img');
            
            // Заменить заглушки на реальные изображения
            document.querySelector('.placeholder-logo').style.display = 'none';
            document.querySelector('.placeholder-logo.footer').style.display = 'none';
            
            headerLogo.src = '';
            footerLogo.src = 'path/to/your/logo-white.png';
       
        }

        // Инициализация при загрузке
        window.addEventListener('DOMContentLoaded', loadLogo);