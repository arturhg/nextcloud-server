<!--
 - SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 - SPDX-License-Identifier: AGPL-3.0-or-later
 -->
<template>
	<form id="ldap" class="section">
		<h2>{{ t('user_ldap', 'LDAP/AD integration') }}</h2>

		<div class="ldap__config-selection">
			<NcSelect v-model="selectedConfigId"
				:options="Object.values(ldapConfigStore.ldapConfigs)"
				:input-label="t('user_ldap', 'Select LDAP Config')" />
			<NcButton :label="t('user_ldap','Create New Config')" @click="ldapConfigStore.create">
				<template #icon>
					<Plus :size="20" />
				</template>
			</NcButton>
			<!-- TODO: Find a use for the loading icon -->
			<NcLoadingIcon v-if="false" />
		</div>

		<div v-if="selectedConfig !== undefined" id="ldapSettings">
			<div class="ldap__tab-selection">
				<NcCheckboxRadioSwitch v-for="(tabId, tabLabel) in tabs"
					:key="tabId"
					:button-variant="true"
					:checked.sync="selectedTab"
					:value="tabId"
					name="ldap_selected_tab"
					type="radio"
					button-variant-grouped="horizontal">
					{{ tabLabel }}
				</NcCheckboxRadioSwitch>
			</div>

			<p v-if="!ldapModuleInstalled" class="ldapwarning">
				{{ t('user_ldap', '<b>Warning:</b> The PHP LDAP module is not installed, the backend will not work. Please ask your system administrator to install it.') }}
			</p>

			<ServerTab v-if="selectedTab === 'server'" :ldap-config="selectedConfig" />
			<!-- <UsersTab v-else-if="selectedTab === 'users'" :ldap-config="selectedConfig" />
			<LoginTab v-else-if="selectedTab === 'login'" :ldap-config="selectedConfig" />
			<GroupsTab v-else-if="selectedTab === 'groups'" :ldap-config="selectedConfig" />
			<ExpertTab v-else-if="selectedTab === 'expert'" :ldap-config="selectedConfig" />
			<AdvancedTab v-else-if="selectedTab === 'advanced'" :ldap-config="selectedConfig" /> -->
		</div>
	</form>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import Plus from 'vue-material-design-icons/Plus.vue'

import { t } from '@nextcloud/l10n'
import { NcCheckboxRadioSwitch, NcLoadingIcon, NcSelect, NcButton } from '@nextcloud/vue'

import ServerTab from '../components/SettingsTabs/ServerTab.vue'
// import UsersTab from '../components/SettingsTabs/UsersTab.vue'
// import LoginTab from '../components/SettingsTabs/LoginTab.vue'
// import GroupsTab from '../components/SettingsTabs/GroupsTab.vue'
// import ExpertTab from '../components/SettingsTabs/ExpertTab.vue'
// import AdvancedTab from '../components/SettingsTabs/AdvancedTab.vue'
import { useLDAPConfigStore } from '../store/config'

const selectedTab = 'server'
const selectedConfigId = null
// TODO: Setup from initial state
const ldapModuleInstalled = true

const ldapConfigStore = useLDAPConfigStore()

const selectedConfig = computed(() => ldapConfigStore.ldapConfigs[selectedConfigId ?? 0] ?? { ...ldapConfigStore.defaultLdapConfig })

const tabs = {
	server: t('user_ldap', 'Server'),
	users: t('user_ldap', 'Users'),
	login: t('user_ldap', 'Login Attributes'),
	groups: t('user_ldap', 'Groups'),
	expert: t('user_ldap', 'Expert'),
	advanced: t('user_ldap', 'Advanced'),
}
</script>
<style lang="scss" scoped>
.ldap__config-selection {
	display: flex;
	align-items: center;
	gap: 16px;
}

.ldap__tab-selection {
	display: flex;
}
</style>
