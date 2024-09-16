/**
 * SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
import { defineStore } from 'pinia'
import Vue from 'vue'

import { loadState } from '@nextcloud/initial-state'

import { createConfig, deleteConfig, updateConfig } from '../services/ldapConfigService'
import type { LDAPConfig } from '../models'

/**
 *
 * @param {...any} args
 */
export function useLDAPConfigStore(...args) {
	const store = defineStore('ldapconfig', {
		state: () => ({
			ldapConfigs: loadState('user_ldap', 'ldapConfigs') as Record<string, LDAPConfig>,
			defaultLdapConfig: loadState('user_ldap', 'ldapDefaultConfig') as LDAPConfig,
		}),

		actions: {
			async create() {
				const { configId, config } = await createConfig({ ...this.defaultLdapConfig })
				Vue.set(this.ldapConfigs, configId, config)
			},

			async copy(fromConfigId: string) {
				const { configId, config } = await createConfig({ ...this.ldapConfigs[fromConfigId] })
				Vue.set(this.ldapConfigs, configId, config)
			},

			async remove(configId: string) {
				const result = await deleteConfig(configId)
				if (result === true) {
					Vue.delete(this.ldapConfigs, configId)
				}
			},

			async update(configId: string, config: LDAPConfig) {
				config = await updateConfig(configId, config)
				Vue.set(this.ldapConfigs, configId, config)
			},

			async detectPort() {
				 // TODO
			},

			async detectBaseDN() {
				 // TODO
			},

			async testBaseDN() {
				 // TODO
			},
		},
	})

	return store(...args)
}
