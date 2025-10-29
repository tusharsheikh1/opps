{{-- Modern Footer for Book Selling E-commerce - Enhanced Design --}}
<style>
    .fixed-cart {
        width: 60px;
        height: 60px;
        text-align: center;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        position: fixed;
        bottom: 120px;
        right: 20px;
        z-index: 999;
        box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
        transition: all 0.3s ease;
        font-size: 14px;
        font-weight: 600;
    }

    .fixed-cart:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(79, 70, 229, 0.4);
    }

    .fixed_what {
        position: fixed !important;
        list-style: none;
        right: 20px;
        bottom: 20px;
        z-index: 999999;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .fixed_what a {
        width: 65px;
        height: 65px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        box-shadow: 0 8px 30px rgba(37, 211, 102, 0.4);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }

    .fixed_what a:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 15px 40px rgba(37, 211, 102, 0.5);
    }

    .whatsapp-text {
        background: #ffffff;
        color: #1f2937;
        padding: 12px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        position: relative;
        max-width: 200px;
        line-height: 1.4;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        border: 1px solid #e5e7eb;
        opacity: 0;
        transform: translateX(10px) scale(0.8);
        animation: whatsappTextCycle 20s infinite;
        pointer-events: none;
    }

    .whatsapp-text:after {
        content: '';
        position: absolute;
        top: 50%;
        right: -8px;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-left: 8px solid #ffffff;
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
    }

    .whatsapp-text:before {
        content: '';
        position: absolute;
        top: 50%;
        right: -9px;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-left: 9px solid #e5e7eb;
        border-top: 9px solid transparent;
        border-bottom: 9px solid transparent;
        z-index: -1;
    }

    .fixed_what:hover .whatsapp-text {
        animation-play-state: paused;
        opacity: 1 !important;
        transform: translateX(0) scale(1) !important;
        pointer-events: auto;
    }

    @keyframes whatsappTextCycle {
        0% { opacity: 0; transform: translateX(10px) scale(0.8); }
        5% { opacity: 1; transform: translateX(0) scale(1); }
        50% { opacity: 1; transform: translateX(0) scale(1); }
        55% { opacity: 0; transform: translateX(10px) scale(0.8); }
        100% { opacity: 0; transform: translateX(10px) scale(0.8); }
    }

    .whatsapp-text.active {
        animation: whatsappNotificationPulse 0.6s ease-out;
    }

    @keyframes whatsappNotificationPulse {
        0% { transform: translateX(0) scale(1); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); }
        50% { transform: translateX(0) scale(1.05); box-shadow: 0 12px 30px rgba(37, 211, 102, 0.3); }
        100% { transform: translateX(0) scale(1); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); }
    }

    @media (max-width: 768px) {
        .fixed_what {
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
        }

        .whatsapp-text {
            max-width: 180px;
            font-size: 13px;
            padding: 10px 14px;
            order: -1;
            transform: translateY(-10px) scale(0.8);
        }

        .whatsapp-text:after {
            bottom: -8px;
            right: 15px;
            transform: none;
            border-top: 8px solid #ffffff;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: none;
        }

        .whatsapp-text:before {
            bottom: -9px;
            right: 14px;
            transform: none;
            border-top: 9px solid #e5e7eb;
            border-left: 9px solid transparent;
            border-right: 9px solid transparent;
            border-bottom: none;
            z-index: -1;
        }

        .fixed_what:hover .whatsapp-text {
            transform: translateY(0) scale(1) !important;
        }
    }

    .fixed_what .fa-whatsapp {
        color: white;
        background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .fixed_what .fa-headset {
        color: white;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    /* WhatsApp Chat Modal */
    .whatsapp-chat-modal {
        position: fixed;
        bottom: 100px;
        right: 20px;
        width: 360px;
        max-width: calc(100vw - 40px);
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
        z-index: 999998;
        opacity: 0;
        transform: translateY(20px) scale(0.95);
        pointer-events: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .whatsapp-chat-modal.active {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    .whatsapp-chat-header {
        background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
        padding: 20px;
        color: white;
        position: relative;
    }

    .whatsapp-chat-header-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .whatsapp-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #25d366;
        flex-shrink: 0;
    }

    .whatsapp-header-info h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }

    .whatsapp-header-info p {
        margin: 2px 0 0;
        font-size: 13px;
        opacity: 0.9;
    }

    .whatsapp-close-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        font-size: 18px;
    }

    .whatsapp-close-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .whatsapp-chat-body {
        padding: 20px;
        max-height: 400px;
        overflow-y: auto;
        background: #ece5dd;
    }

    .whatsapp-message {
        background: white;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 12px;
        position: relative;
        max-width: 85%;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        animation: messageSlideIn 0.3s ease-out;
    }

    @keyframes messageSlideIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .whatsapp-message::before {
        content: '';
        position: absolute;
        left: -8px;
        top: 12px;
        width: 0;
        height: 0;
        border-right: 8px solid white;
        border-top: 6px solid transparent;
        border-bottom: 6px solid transparent;
    }

    .whatsapp-message p {
        margin: 0;
        color: #1f2937;
        font-size: 14px;
        line-height: 1.5;
    }

    .whatsapp-time {
        font-size: 11px;
        color: #667781;
        margin-top: 4px;
        text-align: right;
    }

    .whatsapp-chat-footer {
        padding: 16px 20px;
        background: #f0f2f5;
        border-top: 1px solid #e5e7eb;
    }

    .whatsapp-form {
        display: flex;
        gap: 10px;
    }

    .whatsapp-input {
        flex: 1;
        padding: 12px 16px;
        border: 1px solid #d1d5db;
        border-radius: 24px;
        font-size: 14px;
        outline: none;
        transition: all 0.3s ease;
        background: white;
    }

    .whatsapp-input:focus {
        border-color: #25d366;
        box-shadow: 0 0 0 3px rgba(37, 211, 102, 0.1);
    }

    .whatsapp-send-btn {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
        border: none;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .whatsapp-send-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
    }

    .whatsapp-send-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: scale(1);
    }

    @media (max-width: 768px) {
        .whatsapp-chat-modal {
            bottom: 90px;
            right: 10px;
            width: calc(100vw - 20px);
            max-width: 100%;
        }

        .whatsapp-chat-body {
            max-height: 300px;
        }
    }

    .whatsapp-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.3);
        z-index: 999997;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .whatsapp-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }

    /* Modern Trust Section */
    .trust-section {
        background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
        padding: 50px 0;
        margin-top: 60px;
        border-top: 1px solid #e5e7eb;
    }

    .trust-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        align-items: start;
    }

    .trust-badges-wrapper {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .trust-badge {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(79, 70, 229, 0.1);
    }

    .trust-badge:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 28px rgba(79, 70, 229, 0.15);
        border-color: rgba(79, 70, 229, 0.3);
    }

    .trust-badge-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        font-size: 22px;
        flex-shrink: 0;
    }

    .trust-badge-icon.secure {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .trust-badge-icon.delivery {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    .trust-badge-icon.support {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
    }

    .trust-badge-icon.guarantee {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }

    .trust-badge-text h5 {
        margin: 0 0 4px 0;
        font-size: 15px;
        font-weight: 700;
        color: #1f2937;
    }

    .trust-badge-text p {
        margin: 0;
        font-size: 13px;
        color: #6b7280;
        font-weight: 500;
    }

    .payment-methods {
        background: white;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(79, 70, 229, 0.1);
    }

    .payment-methods h5 {
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 20px 0;
        text-align: center;
    }

    .payment-icons {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        justify-content: center;
        align-items: center;
    }

    .payment-icon {
        background: #f9fafb;
        padding: 10px 18px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 75px;
        height: 48px;
    }

    .payment-icon:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
        border-color: #4f46e5;
        background: white;
    }

    .payment-icon img {
        max-width: 100%;
        max-height: 32px;
        width: auto;
        height: auto;
        object-fit: contain;
    }

    .payment-icon i {
        font-size: 30px;
    }

    .payment-icon.visa i { color: #1434CB; }
    .payment-icon.mastercard i { color: #EB001B; }
    .payment-icon.amex i { color: #006FCF; }
    .payment-icon.ssl i { color: #10b981; }

    /* Modern Footer */
    footer {
        background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%);
        border-top: 1px solid #e5e7eb;
        padding: 70px 0 0;
        margin-top: 0;
    }

    .footer-content {
        padding-bottom: 50px;
    }

    .footer-section {
        margin-bottom: 40px;
    }

    .footer-logo {
        margin-bottom: 25px;
    }

    .footer-logo img {
        max-height: 65px;
        width: auto;
    }

    .footer-description {
        color: #6b7280;
        font-size: 15px;
        line-height: 1.8;
        margin-bottom: 30px;
        font-weight: 400;
    }

    .footer-title {
        color: #1f2937;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 25px;
        position: relative;
        padding-bottom: 15px;
        text-align: left;
        letter-spacing: -0.02em;
    }

    .footer-title:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 4px;
        background: linear-gradient(90deg, #4f46e5, #7c3aed);
        border-radius: 4px;
    }

    @media (max-width: 767px) {
        .footer-title {
            cursor: pointer;
            user-select: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-title::after {
            content: '\f078';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            font-size: 14px;
            color: #4f46e5;
            transition: transform 0.3s ease;
            position: static;
            width: auto;
            height: auto;
            background: none;
        }
        
        .footer-title.active::after {
            transform: rotate(180deg);
        }

        .footer-collapsible {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .footer-collapsible.active {
            max-height: 500px;
        }

        .footer-section:first-child .footer-title,
        .footer-section:last-child .footer-title {
            cursor: default;
            justify-content: flex-start;
        }

        .footer-section:first-child .footer-title::after,
        .footer-section:last-child .footer-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 4px;
            background: linear-gradient(90deg, #4f46e5, #7c3aed);
            border-radius: 4px;
        }

        .footer-section:first-child .footer-collapsible,
        .footer-section:last-child .footer-collapsible {
            max-height: none;
        }
    }

    @media (min-width: 768px) {
        .footer-collapsible {
            max-height: none !important;
        }
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
        text-align: left;
    }

    .footer-links li {
        margin-bottom: 14px;
    }

    .footer-links a {
        color: #6b7280;
        text-decoration: none;
        font-size: 15px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        font-weight: 500;
    }

    .footer-links a:hover {
        color: #4f46e5;
        transform: translateX(5px);
    }

    .footer-links a::before {
        content: '→';
        margin-right: 8px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .footer-links a:hover::before {
        opacity: 1;
    }

    .contact-info {
        list-style: none;
        padding: 0;
        margin: 0;
        text-align: left;
    }

    .contact-info li {
        color: #6b7280;
        font-size: 15px;
        margin-bottom: 18px;
        display: flex;
        align-items: flex-start;
        gap: 15px;
        line-height: 1.7;
        text-align: left;
        font-weight: 500;
    }

    .contact-info i {
        color: #4f46e5;
        font-size: 18px;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .social-links {
        display: flex;
        gap: 12px;
        margin-top: 30px;
        flex-wrap: wrap;
        justify-content: flex-start;
    }

    .social-links a {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .social-links a:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    .app-download {
        margin-top: 30px;
        text-align: left;
    }

    .app-download h5 {
        color: #1f2937;
        margin-bottom: 15px;
        font-size: 16px;
        font-weight: 700;
    }

    .app-download img {
        max-width: 170px;
        height: auto;
        transition: transform 0.3s ease;
        border-radius: 8px;
    }

    .app-download img:hover {
        transform: scale(1.05);
    }

    .footer-bottom {
        background: #1f2937;
        padding: 25px 0;
        text-align: center;
    }

    .footer-bottom p {
        color: #d1d5db;
        font-size: 14px;
        margin: 0;
        font-weight: 500;
    }

    /* Book Categories */
    .book-categories {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 25px;
        justify-content: flex-start;
    }

    .book-category-tag {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        color: #4f46e5;
        padding: 8px 16px;
        border-radius: 24px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }

    .book-category-tag:hover {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    /* Responsive */
    @media (max-width: 991px) {
        footer {
            padding: 50px 0 0;
        }

        .trust-section {
            padding: 40px 0;
        }

        .trust-badges-wrapper {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767px) {
        .trust-section {
            padding: 30px 0;
        }

        .trust-container {
            gap: 20px;
        }

        .trust-badge {
            padding: 15px;
        }

        .trust-badge-icon {
            width: 42px;
            height: 42px;
            font-size: 18px;
        }

        .payment-icon {
            min-width: 65px;
            height: 44px;
        }

        .footer-section {
            text-align: left;
        }
    }
</style>

<script>
// Visual effects and WhatsApp modal functionality
document.addEventListener('DOMContentLoaded', function() {
    // Display current time in WhatsApp message
    const timeElement = document.getElementById('currentTime');
    if (timeElement) {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        timeElement.textContent = `${hours}:${minutes}`;
    }

    const whatsappText = document.querySelector('.whatsapp-text');

    if (whatsappText) {
        function addPulseEffect() {
            whatsappText.classList.add('active');
            setTimeout(() => {
                whatsappText.classList.remove('active');
            }, 600);
        }

        setInterval(() => {
            setTimeout(() => {
                addPulseEffect();
            }, 1000);
        }, 20000);

        setTimeout(() => {
            addPulseEffect();
        }, 1000);
    }

    // WhatsApp Modal Functionality
    const whatsappBtn = document.querySelector('.fixed_what a[title*="WhatsApp"]');
    const whatsappModal = document.getElementById('whatsappChatModal');
    const whatsappOverlay = document.getElementById('whatsappOverlay');
    const whatsappClose = document.querySelector('.whatsapp-close-btn');
    const whatsappForm = document.getElementById('whatsappForm');
    const whatsappInput = document.getElementById('whatsappInput');
    const whatsappPhone = document.querySelector('.fixed_what a[title*="WhatsApp"]')?.getAttribute('href')?.replace('https://wa.me/', '');

    if (whatsappBtn && whatsappModal) {
        whatsappBtn.addEventListener('click', function(e) {
            e.preventDefault();
            whatsappModal.classList.add('active');
            whatsappOverlay.classList.add('active');
            whatsappInput.focus();
        });

        function closeModal() {
            whatsappModal.classList.remove('active');
            whatsappOverlay.classList.remove('active');
        }

        if (whatsappClose) {
            whatsappClose.addEventListener('click', closeModal);
        }

        if (whatsappOverlay) {
            whatsappOverlay.addEventListener('click', closeModal);
        }

        if (whatsappForm) {
            whatsappForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const message = whatsappInput.value.trim();

                if (message && whatsappPhone) {
                    const encodedMessage = encodeURIComponent(message);
                    window.open(`https://wa.me/${whatsappPhone}?text=${encodedMessage}`, '_blank');
                    closeModal();
                    whatsappInput.value = '';
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && whatsappModal.classList.contains('active')) {
                closeModal();
            }
        });
    }

    // Collapsible footer sections for mobile
    if (window.innerWidth <= 767) {
        const footerTitles = document.querySelectorAll('.footer-section .footer-title');

        footerTitles.forEach((title, index) => {
            const parentSection = title.closest('.footer-section');

            if (parentSection && (parentSection.matches(':first-child') || parentSection.matches(':last-child'))) {
                return;
            }

            const collapsibleContent = title.nextElementSibling;
            if (collapsibleContent && collapsibleContent.classList.contains('footer-collapsible')) {
                if (!title.classList.contains('active')) {
                    collapsibleContent.style.maxHeight = '0';
                }

                title.addEventListener('click', function() {
                    this.classList.toggle('active');
                    collapsibleContent.classList.toggle('active');
                    
                    if (collapsibleContent.classList.contains('active')) {
                        collapsibleContent.style.maxHeight = collapsibleContent.scrollHeight + "px";
                    } else {
                        collapsibleContent.style.maxHeight = '0';
                    }
                });
                
                title.classList.add('active');
                collapsibleContent.classList.add('active');
                collapsibleContent.style.maxHeight = collapsibleContent.scrollHeight + "px";
            }
        });
    }
});
</script>

{{-- WhatsApp Chat Modal --}}
<div class="whatsapp-overlay" id="whatsappOverlay"></div>
<div class="whatsapp-chat-modal" id="whatsappChatModal">
    <div class="whatsapp-chat-header">
        <button class="whatsapp-close-btn" type="button">
            <i class="fas fa-times"></i>
        </button>
        <div class="whatsapp-chat-header-content">
            <div class="whatsapp-avatar">
                <i class="fab fa-whatsapp"></i>
            </div>
            <div class="whatsapp-header-info">
                <h4>{{setting('site_name') ?: 'Customer Support'}}</h4>
                <p>Typically replies instantly</p>
            </div>
        </div>
    </div>

    <div class="whatsapp-chat-body">
        <div class="whatsapp-message">
            <p>Assalamualaikum! 👋<br>How may I help you today?</p>
            <div class="whatsapp-time" id="currentTime"></div>
        </div>
    </div>

    <div class="whatsapp-chat-footer">
        <form class="whatsapp-form" id="whatsappForm">
            <input
                type="text"
                class="whatsapp-input"
                id="whatsappInput"
                placeholder="Type your message here..."
                required
            >
            <button type="submit" class="whatsapp-send-btn">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

{{-- Floating Action Buttons --}}
@if (setting('FLOAT_LIVE_CHAT') != 1 || setting('FLOAT_LIVE_CHAT') == "")
    @if(!empty(setting('whatsapp')))
    <li class="fixed_what">
        <a href="https://wa.me/{{setting('whatsapp')}}" title="Chat with us on WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
    </li>
    @endif
@else
<li class="fixed_what">
    <a href="{{ url('/connection/live-chat') }}" title="Live Chat Support">
        <i class="fas fa-headset"></i>
    </a>
</li>
@endif

<li class="fixed-cart d-none">
    <a href="{{route('cart')}}" style="color: white; text-decoration: none;">
        <div>
            <i class="fas fa-shopping-bag" style="font-size: 20px;"></i>
            <div style="font-size: 12px; margin-top: 2px;">{{Cart::count()}}</div>
        </div>
    </a>
</li>

{{-- Main Footer --}}
<footer>
    {{-- Trust Badges & Payment Methods Section --}}
    <div class="trust-section">
        <div class="container">
            <div class="trust-container">
                {{-- Trust Badges --}}
                <div class="trust-badges-wrapper">
                    <div class="trust-badge">
                        <div class="trust-badge-icon secure">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="trust-badge-text">
                            <h5>100% Secure</h5>
                            <p>Payment Protected</p>
                        </div>
                    </div>

                    <div class="trust-badge">
                        <div class="trust-badge-icon delivery">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <div class="trust-badge-text">
                            <h5>Fast Delivery</h5>
                            <p>Nationwide Shipping</p>
                        </div>
                    </div>

                    <div class="trust-badge">
                        <div class="trust-badge-icon support">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="trust-badge-text">
                            <h5>24/7 Support</h5>
                            <p>Always Here to Help</p>
                        </div>
                    </div>

                    <div class="trust-badge">
                        <div class="trust-badge-icon guarantee">
                            <i class="fas fa-undo-alt"></i>
                        </div>
                        <div class="trust-badge-text">
                            <h5>Easy Returns</h5>
                            <p>7 Days Return Policy</p>
                        </div>
                    </div>
                </div>

                {{-- Payment Methods --}}
                <div class="payment-methods">
                    <h5>We Accept</h5>
                    <div class="payment-icons">
                        <div class="payment-icon visa" title="Visa">
                            <i class="fab fa-cc-visa"></i>
                        </div>
                        <div class="payment-icon mastercard" title="Mastercard">
                            <i class="fab fa-cc-mastercard"></i>
                        </div>
                        <div class="payment-icon amex" title="American Express">
                            <i class="fab fa-cc-amex"></i>
                        </div>

                        @if(!empty(setting('payment_bkash_logo')))
                        <div class="payment-icon" title="bKash">
                            <img src="{{asset('uploads/setting/'.setting('payment_bkash_logo'))}}" alt="bKash">
                        </div>
                        @else
                        <div class="payment-icon" title="bKash" style="background: #E2136E; color: white; font-weight: 600; font-size: 14px;">
                            bKash
                        </div>
                        @endif

                        @if(!empty(setting('payment_nagad_logo')))
                        <div class="payment-icon" title="Nagad">
                            <img src="{{asset('uploads/setting/'.setting('payment_nagad_logo.png'))}}" alt="Nagad">
                        </div>
                        @else
                        <div class="payment-icon" title="Nagad" style="background: #ED1C24; color: white; font-weight: 600; font-size: 14px;">
                            Nagad
                        </div>
                        @endif

                        @if(!empty(setting('payment_rocket_logo')))
                        <div class="payment-icon" title="Rocket">
                            <img src="{{asset('uploads/setting/'.setting('payment_rocket_logo'))}}" alt="Rocket">
                        </div>
                        @else
                        <div class="payment-icon" title="Rocket" style="background: #8B3A9B; color: white; font-weight: 600; font-size: 13px;">
                            Rocket
                        </div>
                        @endif

                        <div class="payment-icon" title="Cash on Delivery" style="font-size: 11px; font-weight: 700; color: #1f2937;">
                            COD
                        </div>

                        <div class="payment-icon ssl" title="SSL Secure">
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container footer-content">
        <div class="row">
            {{-- Company Info --}}
            <div class="col-lg-4 col-md-6 footer-section">
                <div class="footer-logo">
                    <a href="{{route('home')}}">
                        <img src="{{asset('uploads/setting/'.setting('logo'))}}" alt="{{setting('site_name')}}">
                    </a>
                </div>
                <p class="footer-description">
                    {{setting('footer_description') ?: 'Discover your next great read with our extensive collection of books. From bestsellers to hidden gems, we have something for every reader.'}}
                </p>

                {{-- Dynamic Categories --}}
                @php
                    $footerCategories = App\Models\Category::where('status', true)
                        ->orderBy('pos', 'asc')
                        ->take(8)
                        ->get();

                    $footerSubCategories = App\Models\SubCategory::where('status', true)
                        ->orderBy('id', 'asc')
                        ->take(5)
                        ->get();

                    $footerMiniCategories = App\Models\miniCategory::where('status', true)
                        ->orderBy('id', 'asc')
                        ->take(3)
                        ->get();
                @endphp

                <div class="book-categories">
                    @foreach($footerCategories as $category)
                        <a href="{{route('category.product', $category->slug)}}" class="book-category-tag">{{$category->name}}</a>
                    @endforeach

                    @if($footerCategories->count() < 8)
                        @foreach($footerSubCategories as $subCategory)
                            <a href="{{route('subCategory.product', $subCategory->slug)}}" class="book-category-tag">{{$subCategory->name}}</a>
                        @endforeach
                    @endif

                    @if(($footerCategories->count() + $footerSubCategories->count()) < 8)
                        @foreach($footerMiniCategories as $miniCategory)
                            <a href="{{route('miniCategory.product', $miniCategory->slug)}}" class="book-category-tag">{{$miniCategory->name}}</a>
                        @endforeach
                    @endif
                </div>

                {{-- Social Links --}}
                <div class="social-links">
                    @if(!empty(setting('facebook')))
                    <a href="{{setting('facebook')}}" style="background: #1877f2;" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    @endif
                    @if(!empty(setting('instagram')))
                    <a href="{{setting('instagram')}}" style="background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%);" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    @endif
                    @if(!empty(setting('twitter')))
                    <a href="{{setting('twitter')}}" style="background: #1da1f2;" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    @endif
                    @if(!empty(setting('youtube')))
                    <a href="{{setting('youtube')}}" style="background: #ff0000;" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    @endif
                    @if(!empty(setting('linkedin')))
                    <a href="{{setting('linkedin')}}" style="background: #0077b5;" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="col-lg-2 col-md-6 footer-section">
                <h4 class="footer-title">Quick Links</h4>
                <div class="footer-collapsible active">
                    <ul class="footer-links">
                        @foreach($footerPages as $page)
                        <li><a href="{{route('page',['slug'=>$page->name])}}">{{$page->name}}</a></li>
                        @endforeach
                        @foreach(App\Models\Page::where('position',2)->where('status',1)->get() as $page)
                        <li><a href="{{route('page',['slug'=>$page->name])}}">{{$page->name}}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- My Account --}}
            <div class="col-lg-2 col-md-6 footer-section">
                <h4 class="footer-title">My Account</h4>
                <div class="footer-collapsible active">
                    <ul class="footer-links">
                        <li><a href="{{route('cart')}}">Shopping Cart</a></li>
                        @auth
                        <li><a href="{{route('account')}}">My Account</a></li>
                        <li><a href="{{route('order')}}">Order History</a></li>
                        <li><a href="{{route('checkout')}}">Checkout</a></li>
                        <li>
                            <a href="{{route('logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                        @else
                        <li><a href="{{route('login')}}">Login</a></li>
                        <li><a href="{{route('register')}}">Register</a></li>
                        @endauth
                    </ul>
                </div>
            </div>

            {{-- Contact Info --}}
            <div class="col-lg-4 col-md-6 footer-section">
                <h4 class="footer-title">Contact Us</h4>
                <ul class="contact-info">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{setting('SITE_INFO_ADDRESS')}}</span>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span>{{setting('SITE_INFO_SUPPORT_MAIL')}}</span>
                    </li>
                    <li>
                        <i class="fas fa-phone"></i>
                        <span>{{setting('SITE_INFO_PHONE')}}</span>
                    </li>
                </ul>

                {{-- App Download --}}
                @if(setting('android_app'))
                <div class="app-download">
                    <h5>Download Our App</h5>
                    <a href="https://drive.google.com/file/d/16neRUFZf20QHgGXxtjFZdGAqU3kxr492/view?usp=drivesdk">
                        <img src="{{asset('/')}}/assets/uploads/images/google-play-png-logo-3799.png" alt="Download on Google Play">
                    </a>
                </div>
                @endif

                {!! setting('FOOTER_COL_4_HTML') !!}
            </div>
        </div>
    </div>

    {{-- Copyright --}}
    <div class="footer-bottom">
        <div class="container">
            <p>{{setting('copy_right_text') ?: '© 2024 BookStore. All rights reserved.'}}</p>
        </div>
    </div>
</footer>