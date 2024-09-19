/**
 * SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
import { defineStore } from 'pinia'
import Vue, { computed, ref } from 'vue'

import { loadState } from '@nextcloud/initial-state'

import { createConfig, deleteConfig } from '../services/ldapConfigService'
import type { LDAPConfig } from '../models'

export const useLDAPConfigStore = defineStore('ldap-configs', () => {
	const ldapConfigs = ref(loadState('user_ldap', 'ldapConfigs') as Record<string, LDAPConfig>)
	const defaultLdapConfig = ref(loadState('user_ldap', 'ldapDefaultConfig') as LDAPConfig)
	const selectedConfigId = ref<string>(Object.keys(ldapConfigs.value)[0])
	const selectedConfig = computed<LDAPConfig>(() => ldapConfigs.value[selectedConfigId.value])

	/**
	 *
	 */
	async function create() {
		const newConfig = { ...defaultLdapConfig.value }
		const configId = await createConfig(newConfig)
		Vue.set(ldapConfigs, configId, newConfig)
		selectedConfigId.value = configId
		return configId
	}

	/**
	 *
	 * @param fromConfigId
	 */
	async function copy(fromConfigId: string) {
		const newConfig = { ...ldapConfigs.value[fromConfigId] }
		const configId = await createConfig(newConfig)
		Vue.set(ldapConfigs, configId, newConfig)
		selectedConfigId.value = configId
		return configId
	}

	/**
	 *
	 * @param configId
	 */
	async function remove(configId: string) {
		const result = await deleteConfig(configId)
		if (result === true) {
			Vue.delete(ldapConfigs, configId)
		}

		const firstConfigId = Object.keys(ldapConfigs)[0] ?? await create()
		selectedConfigId.value = firstConfigId
	}

	return {
		ldapConfigs,
		selectedConfigId,
		selectedConfig,
		create,
		copy,
		remove,
	}
})
