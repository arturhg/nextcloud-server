<!--
 - SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 - SPDX-License-Identifier: AGPL-3.0-or-later
 -->
<template>
	<fieldset id="ldapWizard2">
		<div>
			<p>
				{{ t('user_name', 'Listing and searching for users is constrained by these criteria:') }}
			</p>
			<p>
				<label for="ldap_userfilter_objectclass">
					{{ t('user_name', 'Only these object classes:') }}
				</label>

				<select id="ldap_userfilter_objectclass"
					multiple="multiple"
					name="ldap_userfilter_objectclass"
					class="multiSelectPlugin" />
			</p>
			<p>
				<label />
				<span class="ldapInputColElement">{{ t('user_name', 'The most common object classes for users are organizationalPerson, person, user, and inetOrgPerson. If you are not sure which object class to select, please consult your directory admin.') }}</span>
			</p>
			<p>
				<label for="ldap_userfilter_groups">
					{{ t('user_name', 'Only from these groups:') }}
				</label>

				<input type="text" class="ldapManyGroupsSupport ldapManyGroupsSearch hidden" placeholder="t('user_name', 'Search groups')">

				<select id="ldap_userfilter_groups"
					multiple="multiple"
					name="ldap_userfilter_groups"
					class="multiSelectPlugin" />
			</p>
			<p class="ldapManyGroupsSupport hidden">
				<label />
				<select class="ldapGroupList ldapGroupListAvailable"
					multiple="multiple"
					aria-describedby="ldapGroupListAvailable_instructions"
					:title="t('user_name', 'Available groups')" />
			</p><p id="ldapGroupListAvailable_instructions" class="hidden-visually">
				{{ t('user_name', 'Available groups') }}
			</p>
			<span class="buttonSpan">
				<button class="ldapGroupListSelect" type="button">&gt;</button><br>
				<button class="ldapGroupListDeselect" type="button">&lt;</button>
			</span>
			<select class="ldapGroupList ldapGroupListSelected"
				multiple="multiple"
				aria-describedby="ldapGroupListSelected_instructions"
				:title="t('user_name', 'Selected groups')" />
			<p id="ldapGroupListSelected_instructions" class="hidden-visually">
				{{ t('user_name', 'Selected groups') }}
			</p>
			<p>
				<label><a id="toggleRawUserFilter" class="ldapToggle">↓ {{ t('user_name', 'Edit LDAP Query') }}</a></label>
			</p>
			<p id="ldapReadOnlyUserFilterContainer" class="hidden ldapReadOnlyFilterContainer">
				<label>{{ t('user_name', 'LDAP Filter:') }}</label>
				<span class="ldapFilterReadOnlyElement ldapInputColElement" />
			</p>
			<p id="rawUserFilterContainer">
				<textarea id="ldap_userlist_filter"
					type="text"
					name="ldap_userlist_filter"
					class="ldapFilterInputElement"
					placeholder="t('user_name', 'Edit LDAP Query')"
					aria-describedby="ldap_userlist_filter_instructions"
					:title="t('user_name', 'The filter specifies which LDAP users shall have access to the %s instance.', { themeName: theme.getName()})" />
			</p>
			<p id="ldap_userlist_filter_instructions" class="hidden-visually">
				{{ t('user_name', 'The filter specifies which LDAP users shall have access to the %s instance.', { themeName: theme.getName()}) }}
			</p>
			<div class="ldapWizardInfo invisible">
&nbsp;
			</div>
			<p class="ldap_count">
				<NcButton class="ldapGetEntryCount ldapGetUserCount" name="ldapGetEntryCount" type="button">
					{{ t('user_name', 'Verify settings and count users') }}
				</NcButton>
				<span id="ldap_user_count" />
			</p>
			{{ wizardControls }}
		</div>
	</fieldset>
</template>

<script lang="ts" setup>
import { defineProps, PropType } from 'vue'

import { t } from '@nextcloud/l10n'
import { NcButton } from '@nextcloud/vue'

import type { LDAPConfig } from '../../services/ldapConfigService'

const { ldapConfig } = defineProps({
	ldapConfig: {
		type: Object as PropType<LDAPConfig>,
		required: true,
	},
})

const wizardControls = ''
const theme = {
	getName() {
		return 'TODO'
	},
}
</script>
