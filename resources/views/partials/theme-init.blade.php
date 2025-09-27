<script type="text/javascript">
    (function() {
        const storedTheme = localStorage.getItem('theme') || 'auto';
        
        if (storedTheme === 'dark') {
            document.documentElement.classList.add('dark');
        } else if (storedTheme === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (prefersDark) {
                document.documentElement.classList.add('dark');
            }
        }
    })();
</script>
