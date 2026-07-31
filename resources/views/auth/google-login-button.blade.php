<div class="google-login">
    <div class="google-login__divider">
        <span></span>
        atau
        <span></span>
    </div>

    <a href="{{ route('auth.google.redirect') }}" class="google-login__btn">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path fill="#4285F4" d="M23.49 12.27c0-.79-.07-1.54-.19-2.27H12v4.51h6.47c-.28 1.48-1.13 2.73-2.4 3.58v3h3.86c2.26-2.09 3.56-5.17 3.56-8.82z" />
            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.86-3c-1.07.72-2.45 1.14-4.07 1.14-3.13 0-5.78-2.11-6.73-4.96H1.29v3.09C3.26 21.3 7.31 24 12 24z" />
            <path fill="#FBBC05" d="M5.27 14.27a7.2 7.2 0 010-4.54v-3.09H1.29a12 12 0 000 10.72l3.98-3.09z" />
            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.31 0 3.26 2.7 1.29 6.64l3.98 3.09C6.22 6.86 8.87 4.75 12 4.75z" />
        </svg>
        Login dengan Google
    </a>
</div>

<style>
    .google-login {
        margin-top: 1rem;
        display: flex;
        width: 100%;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
    }

    .google-login__divider {
        display: flex;
        width: 100%;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.75rem;
        line-height: 1rem;
        color: #9ca3af;
    }

    .google-login__divider span {
        height: 1px;
        flex: 1 1 0%;
        background-color: #e5e7eb;
    }

    html.dark .google-login__divider span {
        background-color: #374151;
    }

    .google-login__btn {
        display: flex;
        width: 100%;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        background-color: #fff;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        text-decoration: none;
        transition: background-color 0.15s ease;
    }

    .google-login__btn:hover {
        background-color: #f9fafb;
    }

    html.dark .google-login__btn {
        border-color: #4b5563;
        background-color: #1f2937;
        color: #e5e7eb;
    }

    html.dark .google-login__btn:hover {
        background-color: #374151;
    }

    .google-login__btn svg {
        height: 1.25rem;
        width: 1.25rem;
        flex-shrink: 0;
    }
</style>
