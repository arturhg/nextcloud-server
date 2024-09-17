<!--
 - SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 - SPDX-License-Identifier: AGPL-3.0-or-later
 -->
<template>
	<fieldset class="ldap-wizard__expert">
		<div class="ldap-wizard__expert__line">
			<strong>{{ t('user_ldap', 'Internal Username') }}</strong>
			<label for="ldap_expert_username_attr">{{ t('user_ldap', 'By default the internal username will be created from the UUID attribute. It makes sure that the username is unique and characters do not need to be converted. The internal username has the restriction that only these characters are allowed: [a-zA-Z0-9_.@-]. Other characters are replaced with their ASCII correspondence or simply omitted. On collisions a number will be added/increased. The internal username is used to identify a user internally. It is also the default name for the user home folder. It is also a part of remote URLs, for instance for all DAV services. With this setting, the default behavior can be overridden. Changes will have effect only on newly mapped (added) LDAP users. Leave it empty for default behavior.') }}</label>
			<NcTextField id="ldap_expert_username_attr"
				autocomplete="off"
				:label="t('user_ldap', 'Internal Username Attribute:')"
				:value.sync="ldapConfig.ldapExpertUsernameAttr"
				:label-outside="true" />
		</div>

		<div class="ldap-wizard__expert__line">
			<strong>{{ t('user_ldap', 'Override UUID detection') }}</strong>
			<label for="ldap_expert_uuid_user_attr">{{ t('user_ldap', 'By default, the UUID attribute is automatically detected. The UUID attribute is used to doubtlessly identify LDAP users and groups. Also, the internal username will be created based on the UUID, if not specified otherwise above. You can override the setting and pass an attribute of your choice. You must make sure that the attribute of your choice can be fetched for both users and groups and it is unique. Leave it empty for default behavior. Changes will have effect only on newly mapped (added) LDAP users and groups.') }}</label>
			<NcTextField id="ldap_expert_uuid_user_attr"
				autocomplete="off"
				:label="t('user_ldap', 'UUID Attribute for Users')"
				:value.sync="ldapConfig.ldapExpertUUIDUserAttr" />
			<NcTextField autocomplete="off"
				:label="t('user_ldap', 'UUID Attribute for Groups')"
				:value.sync="ldapConfig.ldapExpertUUIDGroupAttr" />
		</div>

		<div class="ldap-wizard__expert__line">
			<strong>{{ t('user_ldap', 'Username-LDAP User Mapping') }}</strong>
			{{ t('user_ldap', 'Usernames are used to store and assign metadata. In order to precisely identify and recognize users, each LDAP user will have an internal username. This requires a mapping from username to LDAP user. The created username is mapped to the UUID of the LDAP user. Additionally the DN is cached as well to reduce LDAP interaction, but it is not used for identification. If the DN changes, the changes will be found. The internal username is used all over. Clearing the mappings will have leftovers everywhere. Clearing the mappings is not configuration sensitive, it affects all LDAP configurations! Never clear the mappings in a production environment, only in a testing or experimental stage.') }}
			<NcButton id="ldap_action_clear_user_mappings" type="button" name="ldap_action_clear_user_mappings">
				{{ t('user_ldap', 'Clear Username-LDAP User Mapping') }}
			</NcButton>
			<NcButton id="ldap_action_clear_group_mappings" type="button" name="ldap_action_clear_group_mappings">
				{{ t('user_ldap', 'Clear Groupname-LDAP Group Mapping') }}
			</NcButton>
		</div>
	</fieldset>
</template>

<script lang="ts" setup>
import { computed, defineProps } from 'vue'

import { t } from '@nextcloud/l10n'
import { NcTextField, NcButton } from '@nextcloud/vue'

import { useLDAPConfigStore } from '../../store/config'

const ldapConfigStore = useLDAPConfigStore()

const { ldapConfigId } = defineProps({
	ldapConfigId: {
		type: String,
		required: true,
	},
})

const ldapConfig = computed(() => ldapConfigStore.ldapConfigs[ldapConfigId])
</script>
<style lang="scss" scoped>
.ldap-wizard__expert {
	display: flex;
	flex-direction: column;
	gap: 16px;

	&__line {
		display: flex;
		flex-direction: column;
		padding-left: 32px;
		gap: 4px;
	}
}
</style>
