<!--
 - SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 - SPDX-License-Identifier: AGPL-3.0-or-later
 -->
<template>
	<form class="ldap-wizard">
		<h2>{{ t('user_ldap', 'LDAP/AD integration') }}</h2>

		<div class="ldap-wizard__config-selection">
			<NcSelect v-model="selectedConfigId"
				:options="Object.keys(ldapConfigs)"
				:input-label="t('user_ldap', 'Select LDAP Config')"
				@input="selectedConfigId = $event">
				<template #option="{label: configId}">
					{{ `${configId}: ${ldapConfigs[configId].ldapHost}` }}
				</template>
				<template #selected-option="{label: configId}">
					{{ `${configId}: ${ldapConfigs[configId].ldapHost}` }}
				</template>
			</NcSelect>
			<NcButton :label="t('user_ldap','Create New Config')" @click="ldapConfigsStore.create">
				<template #icon>
					<Plus :size="20" />
				</template>
			</NcButton>
		</div>

		<NcNoteCard v-if="!ldapModuleInstalled" type="warning" :text="t('user_ldap', 'The PHP LDAP module is not installed, the backend will not work. Please ask your system administrator to install it.')" />

		<div v-if="ldapModuleInstalled && selectedConfig !== undefined" class="ldap-wizard__tab-container">
			<div class="ldap-wizard__tab-selection-container">
				<div class="ldap-wizard__tab-selection">
					<NcCheckboxRadioSwitch v-for="(tabLabel, tabId) in leftTabs"
						:key="tabId"
						:button-variant="true"
						:checked.sync="selectedTab"
						:value="tabId"
						type="radio"
						button-variant-grouped="horizontal">
						{{ tabLabel }}
					</NcCheckboxRadioSwitch>
				</div>
				<div class="ldap-wizard__tab-selection">
					<NcCheckboxRadioSwitch v-for="(tabLabel, tabId) in rightTabs"
						:key="tabId"
						:button-variant="true"
						:checked.sync="selectedTab"
						:value="tabId"
						type="radio"
						button-variant-grouped="horizontal">
						{{ tabLabel }}
					</NcCheckboxRadioSwitch>
				</div>
			</div>

			<ServerTab v-if="selectedTab === 'server'" />
			<UsersTab v-else-if="selectedTab === 'users'" />
			<LoginTab v-else-if="selectedTab === 'login'" />
			<GroupsTab v-else-if="selectedTab === 'groups'" />
			<ExpertTab v-else-if="selectedTab === 'expert'" />
			<AdvancedTab v-else-if="selectedTab === 'advanced'" />

			<WizardControls class="ldap-wizard__controls" />
		</div>
	</form>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

import Plus from 'vue-material-design-icons/Plus.vue'

import { t } from '@nextcloud/l10n'
import { loadState } from '@nextcloud/initial-state'
import { NcCheckboxRadioSwitch, NcSelect, NcButton, NcNoteCard } from '@nextcloud/vue'

import ServerTab from '../components/SettingsTabs/ServerTab.vue'
import UsersTab from '../components/SettingsTabs/UsersTab.vue'
import LoginTab from '../components/SettingsTabs/LoginTab.vue'
import GroupsTab from '../components/SettingsTabs/GroupsTab.vue'
import ExpertTab from '../components/SettingsTabs/ExpertTab.vue'
import AdvancedTab from '../components/SettingsTabs/AdvancedTab.vue'
import WizardControls from '../components/WizardControls.vue'
import { useLDAPConfigsStore } from '../store/configs'
import { updateConfig } from '../services/ldapConfigService'
import { storeToRefs } from 'pinia'

const ldapModuleInstalled = loadState('user_ldap', 'ldapModuleInstalled')

const leftTabs = {
	server: t('user_ldap', 'Server'),
	users: t('user_ldap', 'Users'),
	login: t('user_ldap', 'Login Attributes'),
	groups: t('user_ldap', 'Groups'),
}

const rightTabs = {
	advanced: t('user_ldap', 'Advanced'),
	expert: t('user_ldap', 'Expert'),
}

const ldapConfigsStore = useLDAPConfigsStore()
const { ldapConfigs, selectedConfigId, selectedConfig } = storeToRefs(ldapConfigsStore)

const selectedTab = ref('server')

ldapConfigsStore.$subscribe(async () => {
	if (selectedConfigId === undefined) {
		throw new Error('selectedConfigId should not be undefined')
	}

	await updateConfig(selectedConfigId.value, selectedConfig.value)
})
</script>
<style lang="scss" scoped>
.ldap-wizard {
	padding: 16px;
	max-width: 1000px;

	&__config-selection {
		display: flex;
		align-items: bottom;
		margin-bottom: 8px;
		gap: 16px;
	}

	&__tab-selection-container {
		display: flex;
		justify-content: space-between;
	}

	&__tab-selection {
		display: flex;
		margin-left: -16px;
		margin-bottom: 16px;

		&:last-of-type {
			margin-right: -16px;
		}
	}

	&__tab-container {
		border: 1px solid gray;
		border-radius: var(--border-radius);
		padding: 0 16px 16px 16px;
	}

	&__controls {
		margin-top: 16px;
	}
}
</style>
