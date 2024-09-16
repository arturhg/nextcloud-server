/**
 * SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import path from 'path'

import axios from '@nextcloud/axios'
import { getAppRootUrl } from '@nextcloud/router'

import type { LDAPConfig } from '../models'

const API_ENDPOINT = path.join(getAppRootUrl('user_ldap'), '/api/v1/config')

/**
 *
 * @param config
 */
export async function createConfig(config: LDAPConfig): Promise<{configId: string, config: LDAPConfig}> {
	const response = await axios.post(API_ENDPOINT, config)
	return { configId: response.data.configId as string, config: response.data.config as LDAPConfig }
}

/**
 *
 * @param configId
 * @param config
 */
export async function updateConfig(configId: string, config: LDAPConfig): Promise<LDAPConfig> {
	const response = await axios.put(path.join(API_ENDPOINT, configId), config)
	return response.data as LDAPConfig
}

/**
 *
 * @param configId
 */
export async function deleteConfig(configId: string): Promise<boolean> {
	await axios.delete(path.join(API_ENDPOINT, configId))
	return true
}
