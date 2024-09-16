<!--
 - SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 - SPDX-License-Identifier: AGPL-3.0-or-later
 -->
<template>
	<fieldset id="ldapWizard1">
		<NcButton id="ldap_action_copy_configuration"
			type="button"
			name="ldap_action_copy_configuration"
			aria-describedby="ldap_action_copy_configuration_instructions"
			class="ldapIconCopy icon-default-style"
			:title="t('user_ldap', 'Copy current configuration into new directory binding')">
			<template #icon>
				<ContentCopy :size="20" />
			</template>
		</NcButton>
		<p id="ldap_action_copy_configuration_instructions" class="hidden-visually">
			{{ t('user_ldap', 'Copy current configuration into new directory binding') }}
		</p>
		<NcButton id="ldap_action_delete_configuration"
			type="button"
			aria-describedby="ldap_action_delete_configuration_instructions"
			name="ldap_action_delete_configuration"
			class="icon-delete icon-default-style"
			:title="t('user_ldap', 'Delete the current configuration')">
			<template #icon>
				<Delete :size="20" />
			</template>
		</NcButton>
		<p id="ldap_action_delete_configuration_instructions" class="hidden-visually">
			{{ t('user_ldap', 'Delete the current configuration') }}
		</p>

		<div class="hostPortCombinator">
			<div class="tablerow">
				<div class="tablecell">
					<div class="table">
						<NcTextField id="ldap_host"
							class="host"
							:value.sync="ldapConfig.ldapHost"
							:helper-text="t('user_ldap', 'You can omit the protocol, unless you require SSL. If so, start with ldaps://')"
							:placeholder="t('user_ldap', 'Host')"
							autocomplete="off" />
						<span class="hostPortCombinatorSpan">
							<input id="ldap_port"
								type="number"
								name="ldap_port"
								:placeholder="t('user_ldap', 'Port')">
							<NcButton class="ldapDetectPort" name="ldapDetectPort" @click="detectPort">
								{{ t('user_ldap', 'Detect Port') }}
							</NcButton>
						</span>
					</div>
				</div>
			</div>
			<div class="tablerow">
&nbsp;
			</div>
			<div class="tablerow">
				<NcTextField id="ldap_dn"
					class="tablecell"
					:value.sync="ldapConfig.ldapAgentName"
					:helper-text="t('user_ldap', 'The DN of the client user with which the bind shall be done, e.g. uid=agent,dc=example,dc=com. For anonymous access, leave DN and Password empty.')"
					:placeholder="t('user_ldap', 'User DN')"
					autocomplete="off" />
			</div>

			<div class="tablerow">
				<NcTextField id="ldap_agent_password"
					type="password"
					class="tablecell"
					:helper-text="t('user_ldap', 'For anonymous access, leave DN and Password empty.')"
					:value.sync="ldapConfig.ldapAgentPassword"
					:placeholder="t('user_ldap', 'Password')"
					autocomplete="off" />

				<NcButton class="ldapSaveAgentCredentials" name="ldapSaveAgentCredentials" @click="ldapConfigStore.create(ldapConfig)">
					{{ t('user_ldap', 'Save Credentials') }}
				</NcButton>
			</div>

			<div id="ldap_base" class="tablecell">
				<NcTextArea label="Text area"
					:placeholder="t('user_ldap', 'One Base DN per line')"
					helper-text="t('user_ldap', 'You can specify Base DN for users and groups in the Advanced tab')" />

				<NcButton class="ldapDetectBase" name="ldapDetectBase" type="button">
					{{ t('user_ldap', 'Detect Base DN') }}
				</NcButton>
				<NcButton class="ldapTestBase" name="ldapTestBase" type="button">
					{{ t('user_ldap', 'Test Base DN') }}
				</NcButton>
			</div>

			<div class="tablerow left">
				<input id="ldap_experienced_admin"
					type="checkbox"
					value="1"
					name="ldap_experienced_admin"
					class="tablecell"
					aria-describedby="ldap_experienced_admin_instructions"
					:title="t('user_ldap', 'Avoids automatic LDAP requests. Better for bigger setups, but requires some LDAP knowledge.')">
				<p id="ldap_experienced_admin_instructions" class="hidden-visually">
					{{ t('user_ldap', 'Avoids automatic LDAP requests. Better for bigger setups, but requires some LDAP knowledge.') }}
				</p>
				<label for="ldap_experienced_admin" class="tablecell">
					{{ t('user_ldap', 'Manually enter LDAP filters (recommended for large directories)') }}
				</label>
			</div>
		</div>

		<!-- TODO: What is this -->
		{{ wizardControls }}
	</fieldset>
</template>

<script lang="ts" setup>
import { defineProps, PropType } from 'vue'

import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import Delete from 'vue-material-design-icons/Delete.vue'

import { t } from '@nextcloud/l10n'
import { NcButton } from '@nextcloud/vue'

import { LDAPConfig } from '../../models'
import { useLDAPConfigStore } from '../../store/config'

const ldapConfigStore = useLDAPConfigStore()

const { ldapConfig } = defineProps({
	ldapConfig: {
		type: Object as PropType<LDAPConfig>,
		required: true,
	},
})

const wizardControls = ''
</script>
