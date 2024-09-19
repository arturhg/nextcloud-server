/**
 * SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import path from 'path'

import axios from '@nextcloud/axios'
import { getAppRootUrl, generateOcsUrl } from '@nextcloud/router'

import type { LDAPConfig } from '../models'
import { useLDAPConfigStore } from '../store/configs'
import { showError, showSuccess } from '@nextcloud/dialogs'

const AJAX_ENDPOINT = path.join(getAppRootUrl('user_ldap'), '/ajax')

export type WizardAction =
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
export async function createConfig(config: LDAPConfig) {
	const response = await axios.post(generateOcsUrl('apps/user_ldap/api/v1/config'), config)
	return response.data.ocs.data.configID as string
}

/**
 *
 * @param configId
 * @param config
 */
export async function updateConfig(): Promise<LDAPConfig> {
	const configId = useLDAPConfigStore().selectedConfigId
	const config = useLDAPConfigStore().selectedConfig

	const response = await axios.put(
		generateOcsUrl('apps/user_ldap/api/v1/config/{configId}', { configId }),
		{ configData: config },
	)

	return response.data as LDAPConfig
}

/**
 *
 * @param configId
 */
export async function deleteConfig(configId: string): Promise<boolean> {
	await axios.delete(generateOcsUrl('apps/user_ldap/api/v1/config/{configId}', { configId }))
	// TODO: check status
	return true
}

/**
 * Starts a configuration test.
 * @param configId
 */
export async function testConfiguration(configId: string) {
	const params = new FormData()
	params.set('ldap_serverconfig_chooser', configId)

	const response = await axios.post(
		path.join(AJAX_ENDPOINT, 'testConfiguration.php'),
		params,
	)

	if (response.data.status === 'success') {
		showSuccess(response.data.message)
	} else {
		showError(response.data.message)
	}

	return response.data
}

/**
 *
 * @param subject
 */
export async function clearMapping(subject: 'user' | 'group') {
	const params = new FormData()
	params.set('ldap_clear_mapping', subject)

	const response = await axios.post(
		path.join(AJAX_ENDPOINT, 'clearMappings.php'),
		params,
	)

	return response.data // TODO: check response content
}

/**
 * Calls the wizard endpoint.
 * @param action
 * @param configId
 * @param extraParams
 */
export async function callWizard(action: WizardAction, configId: string, extraParams: Record<string, string> = {}) {
	const params = new FormData()
	params.set('action', action)
	params.set('ldap_serverconfig_chooser', configId)

	Object.entries(extraParams).forEach(([key, value]) => {
		params.set(key, value)
	})

	const response = await axios.post(
		path.join(AJAX_ENDPOINT, 'wizard.php'),
		params,
	)

	if (response.data.status === 'error') {
		showError(response.data.message)
		throw new Error('Error when calling wizard.php')
	}

	return response.data
}
