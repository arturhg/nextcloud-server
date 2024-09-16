<!--
 - SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 - SPDX-License-Identifier: AGPL-3.0-or-later
 -->
<template>
	<fieldset id="ldapWizard4">
		<div>
			<p>
				{{ t('user_ldap', 'Groups meeting these criteria are available in %s:', {themeName: theme.getName()}) }}
			</p>
			<p>
				<label for="ldap_groupfilter_objectclass">
					{{ t('user_ldap', 'Only these object classes:') }}
				</label>

				<select id="ldap_groupfilter_objectclass"
					multiple="multiple"
					name="ldap_groupfilter_objectclass"
					class="multiSelectPlugin" />
			</p>
			<p>
				<label for="ldap_groupfilter_groups">
					{{ t('user_ldap', 'Only from these groups:') }}
				</label>

				<input type="text" class="ldapManyGroupsSupport ldapManyGroupsSearch hidden" placeholder="t('user_ldap', 'Search groups')">

				<select id="ldap_groupfilter_groups"
					multiple="multiple"
					name="ldap_groupfilter_groups"
					class="multiSelectPlugin" />
			</p>
			<p class="ldapManyGroupsSupport hidden">
				<label />
				<select class="ldapGroupList ldapGroupListAvailable"
					multiple="multiple"
					aria-describedby="ldapGroupListAvailable_instructions"
					title="t('user_ldap', 'Available groups')" />
			</p><p id="ldapGroupListAvailable_instructions" class="hidden-visually">
				{{ t('user_ldap', 'Available groups') }}
			</p>
			<span class="buttonSpan">
				<NcButton class="ldapGroupListSelect" type="button">&gt;</NcButton><br>
				<NcButton class="ldapGroupListDeselect" type="button">&lt;</NcButton>
			</span>
			<select class="ldapGroupList ldapGroupListSelected"
				multiple="multiple"
				aria-describedby="ldapGroupListSelected_instructions"
				title="t('user_ldap', 'Selected groups')" />
			<p id="ldapGroupListSelected_instructions" class="hidden-visually">
				{{ t('user_ldap', 'Selected groups') }}
			</p>
			<p>
				<label><a id="toggleRawGroupFilter" class="ldapToggle">↓ {{ t('user_ldap', 'Edit LDAP Query') }}</a></label>
			</p>
			<p id="ldapReadOnlyGroupFilterContainer" class="hidden ldapReadOnlyFilterContainer">
				<label>{{ t('user_ldap', 'LDAP Filter:') }}</label>
				<span class="ldapFilterReadOnlyElement ldapInputColElement" />
			</p>
			<p id="rawGroupFilterContainer" class="invisible">
				<textarea id="ldap_group_filter"
					type="text"
					name="ldap_group_filter"
					placeholder="t('user_ldap', 'Edit LDAP Query')"
					aria-describedby="rawGroupFilterContainer_instructions"
					title="t('user_ldap', 'The filter specifies which LDAP groups shall have access to the {themeName} instance.', {themeName: theme.getName()}) }}" />
			</p><p id="rawGroupFilterContainer_instructions" class="hidden-visually">
				{{ t('user_ldap', 'The filter specifies which LDAP groups shall have access to the {themeName} instance.', {themeName: theme.getName()}) }}
			</p>

			<p />
			<div class="ldapWizardInfo invisible">
&nbsp;
			</div>
			<p class="ldap_count">
				<NcButton class="ldapGetEntryCount ldapGetGroupCount" name="ldapGetEntryCount" type="button">
					{{ t('user_ldap', 'Verify settings and count the groups') }}
				</NcButton>
				<span id="ldap_group_count" />
			</p>

			<!-- TODO: What is this -->
			{{ wizardControls }}
		</div>
	</fieldset>
</template>

<script lang="ts" setup>
import { defineProps, PropType } from 'vue'

import { t } from '@nextcloud/l10n'
import { NcButton } from '@nextcloud/vue'

import { LDAPConfig } from '../../services/ldapConfigService';

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
