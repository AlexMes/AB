module.exports = {
    theme: {
        extend: {
            inset: {
                '12': '3rem',
                '28': '7rem',
            },
            stroke: theme => ({
                current: 'currentColor',
                gray: theme('colors.gray'),
            }),
            minWidth: {
                'screen-75': '75vw',
            },
        },
    },
    variants: {
        whitespace: ['hover'],
        width: ['hover', 'responsive'],
    },
    plugins: [
        require('@tailwindcss/ui'),
    ],
    purge: {
        enabled: false,
    },
};
