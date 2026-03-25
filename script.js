// ===== HEADER SCROLL =====
const header = document.getElementById('header');
const backTop = document.getElementById('backTop');

window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 50);
    backTop.classList.toggle('visible', window.scrollY > 400);
}, { passive: true });

backTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

// ===== HAMBURGER MENU =====
const hamburger = document.getElementById('hamburger');
const navMenu = document.getElementById('navMenu');

hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('active');
    navMenu.classList.toggle('open');
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
        hamburger.classList.remove('active');
        navMenu.classList.remove('open');
    });
});

document.addEventListener('click', (e) => {
    if (!hamburger.contains(e.target) && !navMenu.contains(e.target)) {
        hamburger.classList.remove('active');
        navMenu.classList.remove('open');
    }
});

// ===== ACTIVE LINK =====
const sections = document.querySelectorAll('section[id], footer[id]');
const navLinks = document.querySelectorAll('.nav-link');

window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(section => {
        if (window.scrollY >= section.offsetTop - 120) current = section.getAttribute('id');
    });
    navLinks.forEach(link => {
        link.classList.toggle('active', link.getAttribute('href') === `#${current}`);
    });
}, { passive: true });

// ===== SMOOTH SCROLL =====
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            e.preventDefault();
            window.scrollTo({ top: target.getBoundingClientRect().top + window.scrollY - header.offsetHeight - 10, behavior: 'smooth' });
        }
    });
});

// ===== PARTICLES HERO =====
const particlesContainer = document.getElementById('particles');
if (particlesContainer) {
    const style = document.createElement('style');
    style.textContent = `@keyframes particleFloat { 0%,100%{transform:translateY(0) rotate(0deg);opacity:.08} 33%{transform:translateY(-25px) rotate(120deg);opacity:.25} 66%{transform:translateY(-12px) rotate(240deg);opacity:.15} }`;
    document.head.appendChild(style);

    for (let i = 0; i < 28; i++) {
        const p = document.createElement('div');
        const s = Math.random() * 5 + 2;
        p.style.cssText = `position:absolute;left:${Math.random()*100}%;top:${Math.random()*100}%;width:${s}px;height:${s}px;background:${Math.random()>.5?'#FF7A18':'#FFC247'};border-radius:${Math.random()>.5?'50%':'2px'};opacity:.08;animation:particleFloat ${Math.random()*12+8}s ease-in-out ${Math.random()*8}s infinite;pointer-events:none;`;
        particlesContainer.appendChild(p);
    }
}

// ===== FADE IN ANIMATION =====
const fadeEls = document.querySelectorAll('.curso-card, .fil-card, .inc-card, .test-card, .strip-item, .valor, .imp-item, .dp-card');
const fadeObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
            fadeObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

fadeEls.forEach((el, i) => {
    el.style.cssText += `opacity:0;transform:translateY(28px);transition:opacity 0.6s ease ${(i%4)*80}ms,transform 0.6s ease ${(i%4)*80}ms;`;
    fadeObserver.observe(el);
});

// ===== COUNTER ANIMATION =====
const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const el = entry.target;
            const target = parseInt(el.getAttribute('data-target'));
            const start = performance.now();
            const tick = (now) => {
                const p = Math.min((now - start) / 1800, 1);
                el.textContent = Math.floor((1 - Math.pow(1 - p, 3)) * target);
                if (p < 1) requestAnimationFrame(tick);
                else el.textContent = target;
            };
            requestAnimationFrame(tick);
            counterObserver.unobserve(el);
        }
    });
}, { threshold: 0.5 });

document.querySelectorAll('.imp-n').forEach(c => counterObserver.observe(c));

// ===== FORM SUBMIT =====
const form = document.getElementById('insForm');
if (form) {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = form.querySelector('button[type="submit"]');
        const originalHTML = btn.innerHTML;
        
        // Validar que el email no esté vacío
        const emailInput = form.querySelector('input[name="email"]');
        if (!emailInput || !emailInput.value.trim()) {
            alert('❌ Por favor ingresa tu correo electrónico');
            emailInput.focus();
            return;
        }
        
        // Mostrar estado de envío
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        btn.disabled = true;
        
        try {
            // Crear FormData con los datos del formulario
            const formData = new FormData(form);
            
            // Verificar que los datos están llegando correctamente
            const datosEnviados = Object.fromEntries(formData);
            console.log('Enviando datos:', datosEnviados);
            
            // Validar que el email está presente
            if (!datosEnviados.email) {
                throw new Error('El correo electrónico es obligatorio');
            }
            
            // Enviar al servidor
            const response = await fetch('guardar_solicitud.php', {
                method: 'POST',
                body: formData
            });
            
            // Verificar si la respuesta es OK
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Error response:', errorText);
                throw new Error(`Error HTTP: ${response.status} - ${errorText}`);
            }
            
            // Obtener respuesta JSON
            const resultado = await response.json();
            
            if (resultado.exito) {
                // ✅ Éxito
                btn.innerHTML = '<i class="fas fa-check-circle"></i> ¡Solicitud enviada!';
                btn.style.background = '#25D366';
                btn.style.border = 'none';
                form.reset();
                
                // Mostrar mensaje de éxito
                alert('✅ ' + resultado.mensaje + '\n\nRevisa tu correo para confirmación.');
                
                // Opcional: confeti
                if (typeof createConfetti === 'function') {
                    createConfetti();
                }
                
            } else {
                // ❌ Error del servidor
                throw new Error(resultado.mensaje || 'Error al procesar la solicitud');
            }
            
        } catch (error) {
            // Error de conexión o validación
            console.error('Error detallado:', error);
            
            btn.innerHTML = '<i class="fas fa-exclamation-circle"></i> Error';
            btn.style.background = '#FF6B6B';
            btn.style.border = 'none';
            
            // Mensaje más amigable según el error
            let mensajeError = 'No se pudo enviar la solicitud.\n\n';
            if (error.message.includes('400')) {
                mensajeError += 'Verifica que todos los campos estén correctamente llenados:\n';
                mensajeError += '- Nombre completo\n';
                mensajeError += '- Correo electrónico válido\n';
                mensajeError += '- Programa seleccionado';
            } else if (error.message.includes('Failed to fetch')) {
                mensajeError += 'Error de conexión con el servidor.\n';
                mensajeError += 'Asegúrate de que XAMPP esté ejecutándose.';
            } else {
                mensajeError += error.message;
            }
            
            alert('❌ ' + mensajeError);
        }
        
        // Restaurar botón después de 3 segundos
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.style.background = '';
            btn.disabled = false;
        }, 3000);
    });
}

// ===== CONFETTI =====
function createConfetti() {
    const colors = ['#FF7A18', '#FFC247', '#E65100', '#4ade80', '#60a5fa'];
    const style = document.createElement('style');
    style.textContent = `@keyframes cFall{0%{transform:translateY(0) rotate(0);opacity:1}100%{transform:translateY(-350px) rotate(720deg);opacity:0}}`;
    document.head.appendChild(style);
    for (let i = 0; i < 45; i++) {
        const d = document.createElement('div');
        d.style.cssText = `position:fixed;top:60%;left:${Math.random()*100}%;width:${Math.random()*8+4}px;height:${Math.random()*8+4}px;background:${colors[i%colors.length]};border-radius:${Math.random()>.5?'50%':'2px'};z-index:9999;pointer-events:none;animation:cFall ${Math.random()*1+1.2}s ease-out ${Math.random()*0.5}s forwards;`;
        document.body.appendChild(d);
        setTimeout(() => d.remove(), 3000);
    }
}

// ===== HERO PARALLAX =====
document.addEventListener('mousemove', (e) => {
    const x = (e.clientX / window.innerWidth - 0.5) * 18;
    const y = (e.clientY / window.innerHeight - 0.5) * 18;
    document.querySelectorAll('.hero-glow').forEach((g, i) => {
        const d = i % 2 === 0 ? 1 : -1;
        g.style.transform = `translate(${x*d*0.4}px,${y*d*0.4}px)`;
    });
}, { passive: true });
