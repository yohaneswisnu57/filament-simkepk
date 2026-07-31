<div class="mt-4 flex flex-col items-center gap-3">
    <div class="flex w-full items-center gap-3 text-xs text-gray-400">
        <span class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></span>
        atau
        <span class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></span>
    </div>

    <a
        href="{{ route('auth.google.redirect') }}"
        class="fi-btn flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
    >
        <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
            <path fill="#4285F4" d="M23.49 12.27c0-.79-.07-1.54-.19-2.27H12v4.51h6.47c-.28 1.48-1.13 2.73-2.4 3.58v3h3.86c2.26-2.09 3.56-5.17 3.56-8.82z" />
            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.86-3c-1.07.72-2.45 1.14-4.07 1.14-3.13 0-5.78-2.11-6.73-4.96H1.29v3.09C3.26 21.3 7.31 24 12 24z" />
            <path fill="#FBBC05" d="M5.27 14.27a7.2 7.2 0 010-4.54v-3.09H1.29a12 12 0 000 10.72l3.98-3.09z" />
            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.31 0 3.26 2.7 1.29 6.64l3.98 3.09C6.22 6.86 8.87 4.75 12 4.75z" />
        </svg>
        Login dengan Google
    </a>
</div>
