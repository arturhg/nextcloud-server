<!--
 - SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 - SPDX-License-Identifier: AGPL-3.0-or-later
 -->
<template>
	<fieldset id="ldapWizard3">
		<div>
			<p>
				{{ t('user_ldap', 'When logging in, %s will find the user based on the following attributes:', { themeName: theme.getName() }) }}
			</p>
			<p>
				<label for="ldap_loginfilter_username">
					{{ t('user_ldap', 'LDAP/AD Username:') }}
				</label>

				<input id="ldap_loginfilter_username"
					type="checkbox"
					aria-describedby="ldap_loginfilter_username_instructions"
					name="ldap_loginfilter_username"
					value="1">
			</p>
			<p id="ldap_loginfilter_username_instructions" class="hidden-visually">
				{{ t('user_ldap', 'Allows login against the LDAP/AD username, which is either "uid" or "sAMAccountName" and will be detected.') }}
			</p>
			<p>
				<label for="ldap_loginfilter_email">
					{{ t('user_ldap', 'LDAP/AD Email Address:') }}
				</label>

				<input id="ldap_loginfilter_email"
					type="checkbox"
					aria-describedby="ldap_loginfilter_email_instructions"
					name="ldap_loginfilter_email"
					value="1">
			</p><p id="ldap_loginfilter_email_instructions" class="hidden-visually">
				{{ t('user_ldap', 'Allows login against an email attribute. "mail" and "mailPrimaryAddress" allowed.') }}
			</p>
			<p>
				<label for="ldap_loginfilter_attributes">
					{{ t('user_ldap', 'Other Attributes:') }}
				</label>

				<select id="ldap_loginfilter_attributes"
					multiple="multiple"
					name="ldap_loginfilter_attributes"
					class="multiSelectPlugin" />
			</p>
			<p>
				<label><a id="toggleRawLoginFilter" class="ldapToggle">↓ {{ t('user_ldap', 'Edit LDAP Query') }}</a></label>
			</p>
			<p id="ldapReadOnlyLoginFilterContainer" class="hidden ldapReadOnlyFilterContainer">
				<label>{{ t('user_ldap', 'LDAP Filter:') }}</label>
				<span class="ldapFilterReadOnlyElement ldapInputColElement" />
			</p>
			<p id="rawLoginFilterContainer" class="invisible">
				<textarea id="ldap_login_filter"
					type="text"
					name="ldap_login_filter"
					class="ldapFilterInputElement"
					:placeholder="t('user_ldap', 'Edit LDAP Query')"
					aria-describedby="ldap_login_filter_instructions" />
			</p><p id="ldap_login_filter_instructions" class="hidden-visually">
				{{ t('user_ldap', 'Defines the filter to apply, when login is attempted. \"%%uid\" replaces the username in the login action. Example: \"uid=%%uid\"') }}
			</p>
			<p /><div class="ldapWizardInfo invisible">
&nbsp;
			</div>
			<p class="ldap_verify">
				<input id="ldap_test_loginname"
					type="text"
					name="ldap_test_loginname"
					:placeholder="t('user_ldap', 'Test Loginname')"
					class="ldapVerifyInput"
					aria-describedby="ldap_test_loginname_instructions">
			</p><p id="ldap_test_loginname_instructions" class="hidden-visually">
				{{ t('user_ldap', 'Attempts to receive a DN for the given loginname and the current login filter') }}
			</p>
			<NcButton class="ldapVerifyLoginName"
				name="ldapTestLoginSettings"
				type="button"
				disabled="disabled">
				{{ t('user_ldap', 'Verify settings') }}
			</NcButton>

			<!-- TODO: What is this -->
			{{ wizardControls }}
		</div>
	</fieldset>
</template>

<script lang="ts" setup>
import { defineProps, PropType } from 'vue'

import { t } from '@nextcloud/l10n'
import { NcButton } from '@nextcloud/vue'

import { LDAPConfig } from '../../services/ldapConfigService';

const { ldapConfig } = defineProps({
	ldapConfig: {
		type: Object as PropType<LDAPConfig>,
		required: true,
	},
})

const wizardControls = ''
const theme = {
	getName() {
		return 'TODO'
	},
}
</script>
