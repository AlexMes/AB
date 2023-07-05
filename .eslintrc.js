module.exports = {
  root: true,
  extends: ['plugin:vue/recommended'],
  env: {
    browser: true,
  },
  rules: {
    'no-console': ['error', { allow: ['warn', 'error'] }],
    'no-debugger': 'error',
    'no-alert': 'error',
    'no-else-return': 'error',
    'comma-dangle': ['error', 'always-multiline'],
    'arrow-parens': ['error', 'as-needed'],
    quotes: ['error', 'single'],
    yoda: 'error',
    'no-useless-concat': 'error',
    'no-param-reassign': [
      'error',
      {
        props: true,
        ignorePropertyModificationsFor: [
          'state',
          'acc',
          'e',
          'ctx',
          'req',
          'request',
          'res',
          'response',
          '$scope',
        ],
      },
    ],
    'vue/script-indent': 'error',
		semi: ['error', 'always'],
		'no-extra-semi': 'error',
		'vue/component-name-in-template-casing': ["error", "kebab-case", {
			"registeredComponentsOnly": true,
			"ignores": [],
		}],
		"vue/name-property-casing": ["error", "kebab-case"],
		"vue/html-self-closing": ["error", {
			"html": {
				"void": "always",
				"normal": "never",
				"component": "never"
			},
		}]
  },
};
