<!--
 - SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 - SPDX-License-Identifier: AGPL-3.0-or-later
 -->
<template>
	<fieldset class="ldap-wizard__login">
		{{ t('user_ldap', 'When logging in, {instanceName} will find the user based on the following attributes:', { instanceName }) }}

		<div class="ldap-wizard__login__line ldap-wizard__login__login-attributes">
			<NcCheckboxRadioSwitch :checked="ldapConfig.ldapLoginFilterUsername === '1'"
				:aria-label="t('user_ldap', 'Allows login against the LDAP/AD username, which is either `uid` or `sAMAccountName` and will be detected.')"
				@update:checked="ldapConfig.ldapLoginFilterUsername = $event ? '1' : '0'">
				{{ t('user_ldap', 'LDAP/AD Username') }}
			</NcCheckboxRadioSwitch>

			<NcCheckboxRadioSwitch :checked="ldapConfig.ldapLoginFilterEmail === '1'"
				:aria-label="t('user_ldap', 'Allows login against an email attribute. `mail` and `mailPrimaryAddress` allowed.')"
				@update:checked="ldapConfig.ldapLoginFilterEmail = $event ? '1' : '0'">
				{{ t('user_ldap', 'LDAP/AD Email Address') }}
			</NcCheckboxRadioSwitch>

			<NcSelect v-model="ldapConfig.ldapLoginFilterAttributes"
				:options="['TODO']"
				:input-label="t('user_ldap', 'Other Attributes:')"
				:multiple="true" />
		</div>

		<div class="ldap-wizard__login__line ldap-wizard__login__user-login-filter">
			<NcCheckboxRadioSwitch :checked.sync="editUserLoginFilter">
				{{ t('user_name', 'Edit LDAP Query') }}
			</NcCheckboxRadioSwitch>

			<div v-if="!editUserLoginFilter">
				<label>{{ t('user_name', 'LDAP Filter:') }}</label>
				<span>{{ ldapConfig.ldapLoginFilter }}</span>
			</div>
			<div v-else>
				<NcTextArea :value.sync="ldapConfig.ldapLoginFilter"
					:placeholder="t('user_name', 'Edit LDAP Query')"
					:helper-text="t('user_name', 'Defines the filter to apply, when login is attempted. `%%uid` replaces the username in the login action. Example: `uid=%%uid`')" />
			</div>
		</div>

		<div class="ldap-wizard__login__line">
			<NcTextField :value.sync="testUsername"
				:helper-text="t('user_ldap', 'Attempts to receive a DN for the given loginname and the current login filter')"
				:placeholder="t('user_ldap', 'Test Loginname')"
				autocomplete="off" />

			<NcButton :disabled="enableVerifyButton"
				@click="verifyLoginName">
				{{ t('user_ldap', 'Verify settings') }}
			</NcButton>
		</div>
	</fieldset>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

import { t } from '@nextcloud/l10n'
import { NcButton, NcTextField, NcTextArea, NcCheckboxRadioSwitch, NcSelect } from '@nextcloud/vue'

import { useLDAPConfigStore } from '../../store/configs'

const { selectedConfig: ldapConfig, callWizardAction } = useLDAPConfigStore()

const instanceName = 'TODO'
const testUsername = ref('TODO')
const enableVerifyButton = ref(false)
const editUserLoginFilter = ref(false)

/**
 *
 */
async function verifyLoginName() {
	const { changes: { ldap_test_base: ldapTestBase } } = await callWizardAction('testLoginName', { testUsername: testUsername.value })
}
</script>
<style lang="scss" scoped>
.ldap-wizard__login {
	display: flex;
	flex-direction: column;
	gap: 16px;

	button {
		flex-shrink: 0;
	}

	&__line {
		display: flex;
		align-items: start;
	}

	&__login-attributes {
		display: flex;
		flex-direction: column;
	}

	&__user-login-filter {
		display: flex;
		flex-direction: column;
	}
}
</style>
