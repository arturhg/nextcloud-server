<!--
 - SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 - SPDX-License-Identifier: AGPL-3.0-or-later
 -->
<template>
	<fieldset class="ldap-wizard__server">
		<div class="ldap-wizard__server__line">
			<NcButton :aria-label="t('user_ldap', 'Copy current configuration into new directory binding')"
				@click="() => ldapConfigStore.copy(ldapConfigId)">
				<template #icon>
					<ContentCopy :size="20" />
				</template>
			</NcButton>
			<NcButton :aria-label="t('user_ldap', 'Delete the current configuration')"
				@click="() => ldapConfigStore.remove(ldapConfigId)">
				<template #icon>
					<Delete :size="20" />
				</template>
			</NcButton>
		</div>

		<div class="ldap-wizard__server__line">
			<NcTextField :value.sync="ldapConfig.ldapHost"
				:helper-text="t('user_ldap', 'You can omit the protocol, unless you require SSL. If so, start with ldaps://')"
				:placeholder="t('user_ldap', 'Host')"
				autocomplete="off" />
			<div class="ldap-wizard__server__host__port">
				<NcTextField :value.sync="ldapConfig.ldapPort"
					:placeholder="t('user_ldap', 'Port')"
					type="number"
					autocomplete="off" />
				<NcButton :disabled="currentWizardActions.includes('guessPortAndTLS')" @click="guessPortAndTLS">
					{{ t('user_ldap', 'Detect Port') }}
				</NcButton>
			</div>
		</div>

		<div class="ldap-wizard__server__line">
			<NcTextField :value.sync="ldapConfig.ldapAgentName"
				:helper-text="t('user_ldap', 'The DN of the client user with which the bind shall be done, e.g. uid=agent,dc=example,dc=com. For anonymous access, leave DN and Password empty.')"
				:placeholder="t('user_ldap', 'User DN')"
				autocomplete="off" />
		</div>

		<div class="ldap-wizard__server__line">
			<NcTextField type="password"
				:helper-text="t('user_ldap', 'For anonymous access, leave DN and Password empty.')"
				:value.sync="ldapConfig.ldapAgentPassword"
				:placeholder="t('user_ldap', 'Password')"
				autocomplete="off" />

			<NcButton @click="ldapConfigStore.create">
				{{ t('user_ldap', 'Save Credentials') }}
			</NcButton>
		</div>

		<div class="ldap-wizard__server__line">
			<NcTextArea :label="t('user_ldap', 'Base DN')"
				:value.sync="ldapConfig.ldapBase"
				:placeholder="t('user_ldap', 'One Base DN per line')"
				:helper-text="t('user_ldap', 'You can specify Base DN for users and groups in the Advanced tab')" />

			<NcButton :disabled="currentWizardActions.includes('guessBaseDN')" @click="guessBaseDN">
				{{ t('user_ldap', 'Detect Base DN') }}
			</NcButton>
			<NcButton :disabled="currentWizardActions.includes('countInBaseDN')" @click="countInBaseDN">
				{{ t('user_ldap', 'Test Base DN') }}
			</NcButton>
		</div>

		<div class="ldap-wizard__server__line">
			<NcCheckboxRadioSwitch :checked="ldapConfig.ldapExperiencedAdmin === '1'"
				:aria-label="t('user_ldap', 'Avoids automatic LDAP requests. Better for bigger setups, but requires some LDAP knowledge.')"
				@update:checked="ldapConfig.ldapExperiencedAdmin = $event ? '1' : '0'">
				{{ t('user_ldap', 'Manually enter LDAP filters (recommended for large directories)') }}
			</NcCheckboxRadioSwitch>
		</div>
	</fieldset>
</template>

<script lang="ts" setup>
import { defineProps, computed, ref } from 'vue'

import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import Delete from 'vue-material-design-icons/Delete.vue'

import { t } from '@nextcloud/l10n'
import { NcButton, NcTextField, NcTextArea, NcCheckboxRadioSwitch } from '@nextcloud/vue'

import { useLDAPConfigStore } from '../../store/config'
import { callWizard } from '../../services/ldapConfigService'

const { ldapConfigId } = defineProps({
	ldapConfigId: {
		type: String,
		required: true,
	},
})

const ldapConfigStore = useLDAPConfigStore()

const ldapConfig = computed(() => ldapConfigStore.ldapConfigs[ldapConfigId])
const usersCount = ref<number|undefined>(undefined)
const currentWizardActions = ref<string[]>([])

/**
 *
 */
async function guessPortAndTLS() {
	currentWizardActions.value.push('guessPortAndTLS')
	const { changes: { ldap_port: ldapPort } } = await callWizard('guessPortAndTLS', ldapConfigId)
	ldapConfig.value.ldapPort = ldapPort
	currentWizardActions.value.splice(currentWizardActions.value.indexOf('guessPortAndTLS'), 1)
}

/**
 *
 */
async function guessBaseDN() {
	currentWizardActions.value.push('guessBaseDN')
	const { changes: { ldap_base: ldapBase } } = await callWizard('guessBaseDN', ldapConfigId)
	ldapConfig.value.ldapBase = ldapBase
	currentWizardActions.value.splice(currentWizardActions.value.indexOf('guessPortAndTLS'), 1)
}

/**
 *
 */
async function countInBaseDN() {
	currentWizardActions.value.push('countInBaseDN')
	const { changes: { ldap_test_base: ldapTestBase } } = await callWizard('countInBaseDN', ldapConfigId)
	usersCount.value = ldapTestBase
	currentWizardActions.value.splice(currentWizardActions.value.indexOf('guessPortAndTLS'), 1)
}

</script>
<style lang="scss" scoped>
.ldap-wizard__server {
	display: flex;
	flex-direction: column;
	gap: 16px;

	button {
		flex-shrink: 0;
	}

	&__line {
		display: flex;
		align-items: start;
		gap: 16px;
	}

	&__host__port {
		display: flex;
		align-items: center;
		flex-shrink: 0;
		gap: 16px;
	}
}
</style>
