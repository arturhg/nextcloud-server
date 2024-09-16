<!--
 - SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 - SPDX-License-Identifier: AGPL-3.0-or-later
 -->
<template>
	<form class="ldap-wizard">
		<h2>{{ t('user_ldap', 'LDAP/AD integration') }}</h2>

		<div class="ldap-wizard__config-selection">
			<NcSelect v-model="selectedConfigId"
				:options="selectOptions"
				:input-label="t('user_ldap', 'Select LDAP Config')" />
			<NcButton :label="t('user_ldap','Create New Config')" @click="ldapConfigStore.create">
				<template #icon>
					<Plus :size="20" />
				</template>
			</NcButton>
			<!-- TODO: Find a use for the loading icon -->
			<NcLoadingIcon v-if="false" />
		</div>

		<div v-if="selectedConfigId !== undefined" class="ldap-wizard__tab-container">
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

			<!-- TODO: change ldapwarning -->
			<div v-if="!ldapModuleInstalled" class="ldapwarning">
				{{ t('user_ldap', '<b>Warning:</b> The PHP LDAP module is not installed, the backend will not work. Please ask your system administrator to install it.') }}
			</div>

			<ServerTab v-if="selectedTab === 'server'" :ldap-config-id="selectedConfigId" />
			<UsersTab v-else-if="selectedTab === 'users'" :ldap-config-id="selectedConfigId" />
			<LoginTab v-else-if="selectedTab === 'login'" :ldap-config-id="selectedConfigId" />
			<GroupsTab v-else-if="selectedTab === 'groups'" :ldap-config-id="selectedConfigId" />
			<ExpertTab v-else-if="selectedTab === 'expert'" :ldap-config-id="selectedConfigId" />
			<AdvancedTab v-else-if="selectedTab === 'advanced'" :ldap-config-id="selectedConfigId" />

			<WizardControls class="ldap-wizard__controls" :ldap-config-id="selectedConfigId" />
		</div>
	</form>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

import Plus from 'vue-material-design-icons/Plus.vue'

import { t } from '@nextcloud/l10n'
import { NcCheckboxRadioSwitch, NcLoadingIcon, NcSelect, NcButton } from '@nextcloud/vue'

import ServerTab from '../components/SettingsTabs/ServerTab.vue'
import UsersTab from '../components/SettingsTabs/UsersTab.vue'
import LoginTab from '../components/SettingsTabs/LoginTab.vue'
import GroupsTab from '../components/SettingsTabs/GroupsTab.vue'
import ExpertTab from '../components/SettingsTabs/ExpertTab.vue'
import AdvancedTab from '../components/SettingsTabs/AdvancedTab.vue'
import WizardControls from '../components/WizardControls.vue'
import { useLDAPConfigStore } from '../store/config'

const leftTabs = {
	server: t('user_ldap', 'Server'),
	users: t('user_ldap', 'Users'),
	login: t('user_ldap', 'Login Attributes'),
	groups: t('user_ldap', 'Groups'),
}

const rightTabs = {
	expert: t('user_ldap', 'Expert'),
	advanced: t('user_ldap', 'Advanced'),
}

const ldapConfigStore = useLDAPConfigStore()

const selectedTab = ref('server')
// eslint-disable-next-line prefer-const
const selectedConfigId = ref(Object.keys(ldapConfigStore.ldapConfigs)[0] ?? undefined)
// TODO: Setup from initial state
const ldapModuleInstalled = true

const selectOptions = Object.entries(ldapConfigStore.ldapConfigs).map(([configId, config]) => ({
	id: configId,
	label: `${configId}: ${config.ldapHost}`,
}))

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
		padding: 0 16px;
	}

	&__controls {
		margin-top: 16px;
	}
}
</style>
