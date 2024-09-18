/**
 * SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
import { defineStore } from 'pinia'
import Vue, { ref } from 'vue'

import { loadState } from '@nextcloud/initial-state'

import { createConfig, deleteConfig, updateConfig } from '../services/ldapConfigService'
import type { LDAPConfig } from '../models'

export const useLDAPConfigStore = defineStore('ldapconfig', () => {
	const ldapConfigs = ref(loadState('user_ldap', 'ldapConfigs') as Record<string, LDAPConfig>)
	const defaultLdapConfig = ref(loadState('user_ldap', 'ldapDefaultConfig') as LDAPConfig)

	/**
	 *
	 */
	async function create() {
		const { configId, config } = await createConfig({ ...defaultLdapConfig.value })
		Vue.set(ldapConfigs, configId, config)
	}

	/**
	 *
	 * @param fromConfigId
	 */
	async function copy(fromConfigId: string) {
		const { configId, config } = await createConfig({ ...ldapConfigs[fromConfigId] })
		Vue.set(ldapConfigs, configId, config)
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
	}

	/**
	 *
	 * @param configId
	 * @param config
	 */
	async function update(configId: string) {
		const config = await updateConfig(configId, ldapConfigs[configId])
		Vue.set(ldapConfigs, configId, config)
	}

	return {
		ldapConfigs,
		create,
		copy,
		remove,
		update,
	}
})
