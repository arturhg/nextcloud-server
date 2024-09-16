<!--
 - SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 - SPDX-License-Identifier: AGPL-3.0-or-later
 -->
<template>
	<fieldset id="ldapSettings-1">
		<div id="ldapAdvancedAccordion">
			<h3>{{ t('user_ldap', 'Connection Settings') }}</h3>
			<div>
				<p>
					<label for="ldap_configuration_active">{{ t('user_ldap', 'Configuration Active') }}</label>
					<input id="ldap_configuration_active"
						type="checkbox"
						name="ldap_configuration_active"
						value="1"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapConfigurationActive"
						aria-describedby="ldap_configuration_active_instructions"
						:title="t('user_ldap', 'When unchecked, this configuration will be skipped.')">
				</p>
				<p id="ldap_configuration_active_instructions" class="hidden-visually">
					{{ t('user_ldap', 'When unchecked, this configuration will be skipped.') }}
				</p>
				<p>
					<label for="ldap_backup_host">{{ t('user_ldap', 'Backup (Replica) Host') }}</label>
					<input id="ldap_backup_host"
						type="text"
						name="ldap_backup_host"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapBackupHost"
						aria-describedby="ldap_backup_host_instructions"
						:title="t('user_ldap', 'Give an optional backup host. It must be a replica of the main LDAP/AD server.')">
				</p>
				<p id="ldap_backup_host_instructions" class="hidden-visually">
					{{ t('user_ldap', 'Give an optional backup host. It must be a replica of the main LDAP/AD server.') }}
				</p>
				<p>
					<label for="ldap_backup_port">{{ t('user_ldap', 'Backup (Replica) Port') }}</label>
					<input id="ldap_backup_port"
						type="number"
						name="ldap_backup_port"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapBackupPort">
				</p>
				<p>
					<label for="ldap_override_main_server">{{ t('user_ldap', 'Disable Main Server') }}</label>
					<input id="ldap_override_main_server"
						type="checkbox"
						name="ldap_override_main_server"
						value="1"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapOverrideMainServer"
						aria-describedby="ldap_override_main_server_instructions"
						:title="t('user_ldap', 'Only connect to the replica server.')">
				</p>
				<p id="ldap_override_main_server_instructions" class="hidden-visually">
					{{ t('user_ldap', 'Only connect to the replica server.') }}
				</p>
				<p>
					<label for="ldap_turn_off_cert_check">{{ t('user_ldap', 'Turn off SSL certificate validation.') }}</label>
					<input id="ldap_turn_off_cert_check"
						type="checkbox"
						name="ldap_turn_off_cert_check"
						aria-describedby="ldap_turn_off_cert_check_instructions"
						:title="t('user_ldap', 'Not recommended, use it for testing only! If connection only works with this option, import the LDAP server\'s SSL certificate in your {serverName} server.', { serverName: theme.getName() })"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapTurnOffCertCheck"
						value="1">
				</p>
				<p id="ldap_turn_off_cert_check_instructions" class="hidden-visually">
					{{ t('user_ldap', 'Not recommended, use it for testing only! If connection only works with this option, import the LDAP server\'s SSL certificate in your {serverName} server.', { serverName: theme.getName() }) }}
				</p>
				<br>
				<p>
					<label for="ldap_cache_ttl">{{ t('user_ldap', 'Cache Time-To-Live') }}</label>
					<input id="ldap_cache_ttl"
						type="number"
						name="ldap_cache_ttl"
						aria-describedby="ldap_cache_ttl_instructions"
						:title="t('user_ldap', 'in seconds. A change empties the cache.')"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapCacheTtl">
				</p>
				<p id="ldap_cache_ttl_instructions" class="hidden-visually">
					{{ t('user_ldap', 'in seconds. A change empties the cache.') }}
				</p>
			</div>
			<h3>{{ t('user_ldap', 'Directory Settings') }}</h3>
			<div>
				<p>
					<label for="ldap_display_name">{{ t('user_ldap', 'User Display Name Field') }}</label>
					<input id="ldap_display_name"
						type="text"
						name="ldap_display_name"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapDisplayName"
						aria-describedby="ldap_display_name_instructions"
						:title="t('user_ldap', 'The LDAP attribute to use to generate the user\'s display name.')">
				</p>
				<p id="ldap_display_name_instructions" class="hidden-visually">
					{{ t('user_ldap', 'The LDAP attribute to use to generate the user\'s display name.') }}
				</p>
				<p>
					<label for="ldap_user_display_name_2">{{ t('user_ldap', '2nd User Display Name Field') }}</label>
					<input id="ldap_user_display_name_2"
						type="text"
						name="ldap_user_display_name_2"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapUserDisplayName2"
						aria-describedby="ldap_user_display_name_2_instructions"
						:title="t('user_ldap', 'Optional. An LDAP attribute to be added to the display name in brackets. Results in e.g. »John Doe (john.doe@example.org)«.')">
				</p>
				<p id="ldap_user_display_name_2_instructions" class="hidden-visually">
					{{ t('user_ldap', 'Optional. An LDAP attribute to be added to the display name in brackets. Results in e.g. »John Doe (john.doe@example.org)«.') }}
				</p>
				<p>
					<label for="ldap_base_users">{{ t('user_ldap', 'Base User Tree') }}</label><textarea id="ldap_base_users"
						name="ldap_base_users"
						:placeholder="t('user_ldap', 'One User Base DN per line')"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapBaseUsers"
						aria-describedby="ldap_base_users_instructions"
						:title="t('user_ldap', 'Base User Tree')" />
				</p>
				<p id="ldap_base_users_instructions" class="hidden-visually">
					{{ t('user_ldap', 'Base User Tree') }}
				</p>
				<p>
					<label for="ldap_attributes_for_user_search">{{ t('user_ldap', 'User Search Attributes') }}</label><textarea id="ldap_attributes_for_user_search"
						name="ldap_attributes_for_user_search"
						:placeholder="t('user_ldap', 'Optional; one attribute per line')"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapAttributesForUserSearch"
						aria-describedby="ldap_attributes_for_user_search_instructions"
						:title="t('user_ldap', 'User Search Attributes')" />
				</p>
				<p id="ldap_attributes_for_user_search_instructions" class="hidden-visually">
					{{ t('user_ldap', 'User Search Attributes') }}
				</p>
				<p>
					<label for="ldap_mark_remnants_as_disabled">{{ t('user_ldap', 'Disable users missing from LDAP') }}</label>
					<input id="ldap_mark_remnants_as_disabled"
						type="checkbox"
						name="ldap_mark_remnants_as_disabled"
						value="1"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapMarkRemnantsAsDisabled"
						aria-describedby="ldap_mark_remnants_as_disabled_instructions"
						:title="t('user_ldap', 'When switched on, users imported from LDAP which are then missing will be disabled')">
				</p>
				<p id="ldap_mark_remnants_as_disabled_instructions" class="hidden-visually">
					{{ t('user_ldap', 'When switched on, users imported from LDAP which are then missing will be disabled') }}
				</p>
				<p>
					<label for="ldap_group_display_name">{{ t('user_ldap', 'Group Display Name Field') }}</label>
					<input id="ldap_group_display_name"
						type="text"
						name="ldap_group_display_name"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapGroupDisplayName"
						aria-describedby="ldap_group_display_name_instructions"
						:title="t('user_ldap', 'The LDAP attribute to use to generate the groups\'s display name.')">
				</p>
				<p id="ldap_group_display_name_instructions" class="hidden-visually">
					{{ t('user_ldap', 'The LDAP attribute to use to generate the groups\'s display name.') }}
				</p>
				<p>
					<label for="ldap_base_groups">{{ t('user_ldap', 'Base Group Tree') }}</label><textarea id="ldap_base_groups"
						name="ldap_base_groups"
						:placeholder="t('user_ldap', 'One Group Base DN per line')"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapBaseGroups"
						aria-describedby="ldap_base_groups_instructions"
						:title="t('user_ldap', 'Base Group Tree')" />
				</p>
				<p id="ldap_base_groups_instructions" class="hidden-visually">
					{{ t('user_ldap', 'Base Group Tree') }}
				</p>
				<p>
					<label for="ldap_attributes_for_group_search">{{ t('user_ldap', 'Group Search Attributes') }}</label><textarea id="ldap_attributes_for_group_search"
						name="ldap_attributes_for_group_search"
						:placeholder="t('user_ldap', 'Optional; one attribute per line')"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapAttributesForGroupSearch"
						aria-describedby="ldap_attributes_for_group_search_instructions"
						:title="t('user_ldap', 'Group Search Attributes')" />
				</p>
				<p id="ldap_attributes_for_group_search_instructions" class="hidden-visually">
					{{ t('user_ldap', 'Group Search Attributes') }}
				</p>
				<p>
					<label for="ldap_group_member_assoc_attribute">{{ t('user_ldap', 'Group-Member association') }}</label><select id="ldap_group_member_assoc_attribute" name="ldap_group_member_assoc_attribute" :data-default="ldapConfigStore.defaultLdapConfig.ldapGroupMemberAssocAttribute">
						<option value="uniqueMember" :selected="ldap_group_member_assoc_attribute === 'uniqueMember'">
							{{ t('user_ldap', 'uniqueMember') }}
						</option>
						<option value="memberUid" :selected="ldap_group_member_assoc_attribute === 'memberUid'">
							{{ t('user_ldap', 'memberUid') }}
						</option>
						<option value="member" :selected="ldap_group_member_assoc_attribute === 'member'">
							{{ t('user_ldap', 'member (AD)') }}
						</option>
						<option value="gidNumber" :selected="ldap_group_member_assoc_attribute === 'gidNumber'">
							{{ t('user_ldap', 'gidNumber') }}
						</option>
						<option value="zimbraMailForwardingAddress" :selected="ldap_group_member_assoc_attribute === 'zimbraMailForwardingAddress'">
							{{ t('user_ldap', 'zimbraMailForwardingAddress') }}
						</option>
					</select>
				</p>
				<p>
					<label for="ldap_dynamic_group_member_url">{{ t('user_ldap', 'Dynamic Group Member URL') }}</label>
					<input id="ldap_dynamic_group_member_url"
						type="text"
						name="ldap_dynamic_group_member_url"
						aria-describedby="ldap_dynamic_group_member_url_instructions"
						:title="t('user_ldap', 'The LDAP attribute that on group objects contains an LDAP search URL that determines what objects belong to the group. (An empty setting disables dynamic group membership functionality.)')"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapDynamicGroupMemberUrl">
				</p>
				<p id="ldap_dynamic_group_member_url_instructions" class="hidden-visually">
					{{ t('user_ldap', 'The LDAP attribute that on group objects contains an LDAP search URL that determines what objects belong to the group. (An empty setting disables dynamic group membership functionality.)') }}
				</p>
				<p>
					<label for="ldap_nested_groups">{{ t('user_ldap', 'Nested Groups') }}</label>
					<input id="ldap_nested_groups"
						type="checkbox"
						name="ldap_nested_groups"
						value="1"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapNestedGroups"
						aria-describedby="ldap_nested_groups_instructions"
						:title="t('user_ldap', 'When switched on, groups that contain groups are supported. (Only works if the group member attribute contains DNs.)')">
				</p>
				<p id="ldap_nested_groups_instructions" class="hidden-visually">
					{{ t('user_ldap', 'When switched on, groups that contain groups are supported. (Only works if the group member attribute contains DNs.)') }}
				</p>
				<p>
					<label for="ldap_paging_size">{{ t('user_ldap', 'Paging chunksize') }}</label>
					<input id="ldap_paging_size"
						type="number"
						name="ldap_paging_size"
						aria-describedby="ldap_paging_size_instructions"
						:title="t('user_ldap', 'Chunksize used for paged LDAP searches that may return bulky results like user or group enumeration. (Setting it 0 disables paged LDAP searches in those situations.)')"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapPagingSize">
				</p>
				<p id="ldap_paging_size_instructions" class="hidden-visually">
					{{ t('user_ldap', 'Chunksize used for paged LDAP searches that may return bulky results like user or group enumeration. (Setting it 0 disables paged LDAP searches in those situations.)') }}
				</p>
				<p>
					<label for="ldap_turn_on_pwd_change">{{ t('user_ldap', 'Enable LDAP password changes per user') }}</label><span class="inlinetable"><span class="tablerow left"><input id="ldap_turn_on_pwd_change"
						type="checkbox"
						name="ldap_turn_on_pwd_change"
						value="1"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapTurnOnPwdChange"
						aria-describedby="ldap_turn_on_pwd_change_instructions"
						:title="t('user_ldap', 'Allow LDAP users to change their password and allow Super Administrators and Group Administrators to change the password of their LDAP users. Only works when access control policies are configured accordingly on the LDAP server. As passwords are sent in plaintext to the LDAP server, transport encryption must be used and password hashing should be configured on the LDAP server.')"><p id="ldap_turn_on_pwd_change_instructions" class="hidden-visually">{{ t('user_ldap', 'Allow LDAP users to change their password and allow Super Administrators and Group Administrators to change the password of their LDAP users. Only works when access control policies are configured accordingly on the LDAP server. As passwords are sent in plaintext to the LDAP server, transport encryption must be used and password hashing should be configured on the LDAP server.') }}</p><span class="tablecell">{{ t('user_ldap', '(New password is sent as plain text to LDAP)') }}</span></span>
					</span><br>
				</p>
				<p>
					<label for="ldapConfigStore.defaultLdapConfig.ldapPpolicyDn">{{ t('user_ldap', 'Default password policy DN') }}</label>
					<input id="ldapConfigStore.defaultLdapConfig.ldapPpolicyDn"
						type="text"
						name="ldapConfigStore.defaultLdapConfig.ldapPpolicyDn"
						aria-describedby="ldapConfigStore.defaultLdapConfig.ldapPpolicyDnInstructions"
						:title="t('user_ldap', 'The DN of a default password policy that will be used for password expiry handling. Works only when LDAP password changes per user are enabled and is only supported by OpenLDAP. Leave empty to disable password expiry handling.')"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapPpolicyDn">
				</p>
				<p id="ldapConfigStore.defaultLdapConfig.ldapPpolicyDnInstructions" class="hidden-visually">
					{{ t('user_ldap', 'The DN of a default password policy that will be used for password expiry handling. Works only when LDAP password changes per user are enabled and is only supported by OpenLDAP. Leave empty to disable password expiry handling.') }}
				</p>
			</div>
			<h3>{{ t('user_ldap', 'Special Attributes') }}</h3>
			<div>
				<p>
					<label for="ldap_quota_attr">{{ t('user_ldap', 'Quota Field') }}</label>
					<input id="ldap_quota_attr"
						type="text"
						name="ldap_quota_attr"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapQuotaAttr"
						aria-describedby="ldap_quota_attr_instructions"
						:title="t('user_ldap', 'Leave empty for user\'s default quota. Otherwise, specify an LDAP/AD attribute.')">
				</p>
				<p id="ldap_quota_attr_instructions" class="hidden-visually">
					{{ t('user_ldap', 'Leave empty for user\'s default quota. Otherwise, specify an LDAP/AD attribute.') }}
				</p>
				<p>
					<label for="ldap_quota_def">{{ t('user_ldap', 'Quota Default') }}</label>
					<input id="ldap_quota_def"
						type="text"
						name="ldap_quota_def"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapQuotaDef"
						aria-describedby="ldap_quota_def_instructions"
						:title="t('user_ldap', 'Override default quota for LDAP users who do not have a quota set in the Quota Field.')">
				</p>
				<p id="ldap_quota_def_instructions" class="hidden-visually">
					{{ t('user_ldap', 'Override default quota for LDAP users who do not have a quota set in the Quota Field.') }}
				</p>
				<p>
					<label for="ldap_email_attr">{{ t('user_ldap', 'Email Field') }}</label>
					<input id="ldap_email_attr"
						type="text"
						name="ldap_email_attr"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapEmailAttr"
						aria-describedby="ldap_email_attr_instructions"
						:title="t('user_ldap', 'Set the user\'s email from their LDAP attribute. Leave it empty for default behaviour.')">
				</p>
				<p id="ldap_email_attr_instructions" class="hidden-visually">
					{{ t('user_ldap', 'Set the user\'s email from their LDAP attribute. Leave it empty for default behaviour.') }}
				</p>
				<p>
					<label for="home_folder_naming_rule">{{ t('user_ldap', 'User Home Folder Naming Rule') }}</label>
					<input id="home_folder_naming_rule"
						type="text"
						name="home_folder_naming_rule"
						aria-describedby="home_folder_naming_rule_instructions"
						:title="t('user_ldap', 'Leave empty for username (default). Otherwise, specify an LDAP/AD attribute.')"
						:data-default="ldapConfigStore.defaultLdapConfig.homeFolderNamingRule">
				</p>
				<p id="home_folder_naming_rule_instructions" class="hidden-visually">
					{{ t('user_ldap', 'Leave empty for username (default). Otherwise, specify an LDAP/AD attribute.') }}
				</p>
				<p>
					<label for="ldap_ext_storage_home_attribute"> {{ t('user_ldap', '"$home" Placeholder Field') }}</label>
					<input id="ldap_ext_storage_home_attribute"
						type="text"
						name="ldap_ext_storage_home_attribute"
						aria-describedby="ldap_ext_storage_home_attribute_instructions"
						:title="t('user_ldap', '$home in an external storage configuration will be replaced with the value of the specified attribute')"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapExtStorageHomeAttribute">
				</p>
				<p id="ldap_ext_storage_home_attribute_instructions" class="hidden-visually">
					{{ t('user_ldap', '$home in an external storage configuration will be replaced with the value of the specified attribute') }}
				</p>
			</div>
			<h3>
				{{ t('user_ldap', 'User Profile Attributes') }}
			</h3>
			<div>
				<p>
					<label for="ldap_attr_phone"> {{ t('user_ldap', 'Phone Field') }}</label>
					<input id="ldap_attr_phone"
						type="text"
						name="ldap_attr_phone"
						:title="t('user_ldap', 'User profile Phone will be set from the specified attribute')"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapAttrPhone">
				</p>
				<p>
					<label for="ldap_attr_website"> {{ t('user_ldap', 'Website Field') }}</label>
					<input id="ldap_attr_website"
						type="text"
						name="ldap_attr_website"
						:title="t('user_ldap', 'User profile Website will be set from the specified attribute')"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapAttrWebsite">
				</p>
				<p>
					<label for="ldap_attr_address"> {{ t('user_ldap', 'Address Field') }}</label>
					<input id="ldap_attr_address"
						type="text"
						name="ldap_attr_address"
						:title="t('user_ldap', 'User profile Address will be set from the specified attribute')"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapAttrAddress">
				</p>
				<p>
					<label for="ldap_attr_twitter"> {{ t('user_ldap', 'Twitter Field') }}</label>
					<input id="ldap_attr_twitter"
						type="text"
						name="ldap_attr_twitter"
						:title="t('user_ldap', 'User profile Twitter will be set from the specified attribute')"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapAttrTwitter">
				</p>
				<p>
					<label for="ldap_attr_fediverse"> {{ t('user_ldap', 'Fediverse Field') }}</label>
					<input id="ldap_attr_fediverse"
						type="text"
						name="ldap_attr_fediverse"
						:title="t('user_ldap', 'User profile Fediverse will be set from the specified attribute')"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapAttrFediverse">
				</p>
				<p>
					<label for="ldap_attr_organisation"> {{ t('user_ldap', 'Organisation Field') }}</label>
					<input id="ldap_attr_organisation"
						type="text"
						name="ldap_attr_organisation"
						:title="t('user_ldap', 'User profile Organisation will be set from the specified attribute')"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapAttrOrganisation">
				</p>
				<p>
					<label for="ldap_attr_role"> {{ t('user_ldap', 'Role Field') }}</label>
					<input id="ldap_attr_role"
						type="text"
						name="ldap_attr_role"
						:title="t('user_ldap', 'User profile Role will be set from the specified attribute')"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapAttrRole">
				</p>
				<p>
					<label for="ldap_attr_headline"> {{ t('user_ldap', 'Headline Field') }}</label>
					<input id="ldap_attr_headline"
						type="text"
						name="ldap_attr_headline"
						:title="t('user_ldap', 'User profile Headline will be set from the specified attribute')"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapAttrHeadline">
				</p>
				<p>
					<label for="ldap_attr_biography"> {{ t('user_ldap', 'Biography Field') }}</label>
					<input id="ldap_attr_biography"
						type="text"
						name="ldap_attr_biography"
						:title="t('user_ldap', 'User profile Biography will be set from the specified attribute')"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapAttrBiography">
				</p>
				<p>
					<label for="ldap_attr_birthdate"> {{ t('user_ldap', 'Birthdate Field') }}</label>
					<input id="ldap_attr_birthdate"
						type="text"
						name="ldap_attr_birthdate"
						:title="t('user_ldap', 'User profile Date of birth will be set from the specified attribute')"
						:data-default="ldapConfigStore.defaultLdapConfig.ldapAttrBirthdate">
				</p>
			</div>
		</div>
		<!-- TODO: What is this -->
		{{ settingControls }}
	</fieldset>
</template>

<script lang="ts" setup>
import { defineProps, PropType } from 'vue'

import { t } from '@nextcloud/l10n'

import { LDAPConfig } from '../../models'
import { useLDAPConfigStore } from '../../store/config'

const { ldapConfig } = defineProps({
	ldapConfig: {
		type: Object as PropType<LDAPConfig>,
		required: true,
	},
})

const ldapConfigStore = useLDAPConfigStore()

const settingControls = ''
const theme = {
	getName() {
		return 'TODO'
	},
}
</script>
