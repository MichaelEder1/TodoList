module.exports = {
    purge: {
        enabled: true,
        content: ['./dist/**/*.html', './dist/**/*.php']
    },
    darkMode: false, // or 'media' or 'class'
    theme: {
        extend: {
            animation: {
                buttonAnimation: 'transform 80ms ease-in'
            },
            boxShadow: {
                customBoxShadow: '0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22)'
            },
            width: {
                '2/1': '200%'
            },
            fontFamily: {
                "sans": ["Montserrat, Quicksand, sans-serif"],
            },
            fontSize: {
                "xxs": '.45rem',
            },
        }
    },
    variants: {
        extend: {},
    },
    plugins: [],
}
