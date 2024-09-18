/**
 * SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import path from 'path'

import axios from '@nextcloud/axios'
import { getAppRootUrl, generateOcsUrl } from '@nextcloud/router'

import type { LDAPConfig } from '../models'

const APP_URL = getAppRootUrl('user_ldap')
const AJAX_ENDPOINT = path.join(APP_URL, '/ajax')

const OCS_APP_URL = generateOcsUrl('apps/user_ldap')
const CONFIG_API_ENDPOINT = path.join(OCS_APP_URL, '/api/v1/config')

console.log(OCS_APP_URL)
console.log(CONFIG_API_ENDPOINT)

type WizardAction =
	'guessPortAndTLS' |
	'guessBaseDN' |
	'detectEmailAttribute' |
	'detectUserDisplayNameAttribute' |
	'determineGroupMemberAssoc' |
	'determineUserObjectClasses' |
	'determineGroupObjectClasses' |
	'determineGroupsForUsers' |
	'determineGroupsForGroups' |
	'determineAttributes' |
	'getUserListFilter' |
	'getUserLoginFilter' |
	'getGroupFilter' |
	'countUsers' |
	'countGroups' |
	'countInBaseDN' |
	'testLoginName'

/**
 *
 * @param config
 */
export async function createConfig(
	config: LDAPConfig,
): Promise<{ configId: string; config: LDAPConfig }> {
	const response = await axios.post(CONFIG_API_ENDPOINT, config)
	return {
		configId: response.data.configId as string,
		config: response.data.config as LDAPConfig,
	}
}

/**
 *
 * @param configId
 * @param config
 */
export async function updateConfig(
	configId: string,
	config: LDAPConfig,
): Promise<LDAPConfig> {
	const response = await axios.put(
		path.join(CONFIG_API_ENDPOINT, configId),
		config,
	)
	return response.data as LDAPConfig
}

/**
 *
 * @param configId
 */
export async function deleteConfig(configId: string): Promise<boolean> {
	await axios.delete(path.join(CONFIG_API_ENDPOINT, configId))
	return true
}

/**
 * Starts a configuration test.
 * @param configId
 */
export async function testConfiguration(configId: string) {
	const response = await axios.post(
		path.join(AJAX_ENDPOINT, 'testConfiguration.php'),
		undefined,
		{
			params: {
				ldap_serverconfig_chooser: configId,
			},
		},
	)

	return response.data // TODO: check response content
}

/**
 *
 * @param subject
 */
export async function clearMapping(subject: 'user' | 'group') {
	const response = await axios.post(
		path.join(AJAX_ENDPOINT, 'clearMappings.php'),
		undefined,
		{
			data: {
				ldap_clear_mapping: subject,
			},
		},
	)

	return response.data // TODO: check response content
}

/**
 * Calls the wizard endpoint.
 * @param action
 * @param configId
 */
export async function callWizard(action: WizardAction, configId: string) {
	const params = new FormData()
	params.set('action', action)
	params.set('ldap_serverconfig_chooser', configId)
	const response = await axios.post(
		path.join(AJAX_ENDPOINT, 'wizard.php'),
		params,
	)

	return response.data
}
