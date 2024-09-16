<!--
 - SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 - SPDX-License-Identifier: AGPL-3.0-or-later
 -->
<template>
	<fieldset id="ldapSettings-2">
		<p>
			<strong>{{ t('user_ldap', 'Internal Username') }}</strong>
		</p>
		<p class="ldapIndent">
			{{ t('user_ldap', 'By default the internal username will be created from the UUID attribute. It makes sure that the username is unique and characters do not need to be converted. The internal username has the restriction that only these characters are allowed: [a-zA-Z0-9_.@-]. Other characters are replaced with their ASCII correspondence or simply omitted. On collisions a number will be added/increased. The internal username is used to identify a user internally. It is also the default name for the user home folder. It is also a part of remote URLs, for instance for all DAV services. With this setting, the default behavior can be overridden. Changes will have effect only on newly mapped (added) LDAP users. Leave it empty for default behavior.') }}
		</p>
		<p class="ldapIndent">
			<label for="ldap_expert_username_attr">{{ t('user_ldap', 'Internal Username Attribute:') }}</label>
			<input id="ldap_expert_username_attr"
				type="text"
				name="ldap_expert_username_attr"
				:data-default="ldap_expert_username_attr_default">
		</p>
		<p><strong>{{ t('user_ldap', 'Override UUID detection') }}</strong></p>
		<p class="ldapIndent">
			{{ t('user_ldap', 'By default, the UUID attribute is automatically detected. The UUID attribute is used to doubtlessly identify LDAP users and groups. Also, the internal username will be created based on the UUID, if not specified otherwise above. You can override the setting and pass an attribute of your choice. You must make sure that the attribute of your choice can be fetched for both users and groups and it is unique. Leave it empty for default behavior. Changes will have effect only on newly mapped (added) LDAP users and groups.') }}
		</p>
		<p class="ldapIndent">
			<label for="ldap_expert_uuid_user_attr">{{ t('user_ldap', 'UUID Attribute for Users:') }}</label>
			<input id="ldap_expert_uuid_user_attr"
				type="text"
				name="ldap_expert_uuid_user_attr"
				:data-default="ldap_expert_uuid_user_attr_default">
		</p>
		<p class="ldapIndent">
			<label for="ldap_expert_uuid_group_attr">{{ t('user_ldap', 'UUID Attribute for Groups:') }}</label>
			<input id="ldap_expert_uuid_group_attr"
				type="text"
				name="ldap_expert_uuid_group_attr"
				:data-default="ldap_expert_uuid_group_attr_default">
		</p>
		<p><strong>{{ t('user_ldap', 'Username-LDAP User Mapping') }}</strong></p>
		<p class="ldapIndent">
			{{ t('user_ldap', 'Usernames are used to store and assign metadata. In order to precisely identify and recognize users, each LDAP user will have an internal username. This requires a mapping from username to LDAP user. The created username is mapped to the UUID of the LDAP user. Additionally the DN is cached as well to reduce LDAP interaction, but it is not used for identification. If the DN changes, the changes will be found. The internal username is used all over. Clearing the mappings will have leftovers everywhere. Clearing the mappings is not configuration sensitive, it affects all LDAP configurations! Never clear the mappings in a production environment, only in a testing or experimental stage.') }}
		</p>
		<p class="ldapIndent">
			<button id="ldap_action_clear_user_mappings" type="button" name="ldap_action_clear_user_mappings">
				{{ t('user_ldap', 'Clear Username-LDAP User Mapping') }}
			</button><br><button id="ldap_action_clear_group_mappings" type="button" name="ldap_action_clear_group_mappings">
				{{ t('user_ldap', 'Clear Groupname-LDAP Group Mapping') }}
			</button>
		</p>

		<!-- TODO: What is this -->
		{{ settingControls }}
	</fieldset>
</template>

<script lang="ts" setup>
import { defineProps, PropType } from 'vue'

import { t } from '@nextcloud/l10n'

import { LDAPConfig } from '../../services/ldapConfigService';

const { ldapConfig } = defineProps({
	ldapConfig: {
		type: Object as PropType<LDAPConfig>,
		required: true,
	},
})

const ldap_expert_username_attr_default = ''
const ldap_expert_uuid_user_attr_default = ''
const ldap_expert_uuid_group_attr_default = ''
const settingControls = ''
</script>
