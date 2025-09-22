const js					= require("@eslint/js");
const globals				= require("globals");

// https://github.com/facebook/create-react-app/tree/main/packages/confusing-browser-globals
const restrictedGlobals		= require("confusing-browser-globals");

module.exports = [
	js.configs.recommended,
	{
		rules: {
			"block-scoped-var": "error",
			"consistent-return": "error",
			"dot-notation": "error",
			"eqeqeq": ["error", "smart"],
			"no-console": "error",
			"no-else-return": "error",
			"no-eval": "error",
			"no-extend-native": "error",
			"no-floating-decimal": "error",
			"no-implicit-globals": "error",
			"no-implied-eval": "error",
			"no-lone-blocks": "error",
			"no-loop-func": "error",
			"no-multi-str": "error",
			"no-new": "error",
			"no-new-func": "error",
			"no-new-wrappers": "error",
			"no-octal-escape": "error",
			"no-param-reassign": "error",
			"no-restricted-globals": ["error"].concat(restrictedGlobals),
			"no-return-assign": "error",
			"no-self-compare": "error",
			"no-unmodified-loop-condition": "error",
			"no-useless-call": "error",
			"no-useless-concat": "error",
			"no-useless-return": "error",
			"no-var": "error",
			"no-warning-comments": "warn",
			"no-with": "error",
			"wrap-iife": ["error", "any"],
			"no-shadow-restricted-names": "error",
		},

		languageOptions: {
			ecmaVersion: 2022,
			globals: {
                ...globals.browser,
				disable_emails_settings: false,
				jQuery: false,
				module: false,
			}
		}
	}
];
