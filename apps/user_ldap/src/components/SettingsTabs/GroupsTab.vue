<!--
 - SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 - SPDX-License-Identifier: AGPL-3.0-or-later
 -->
<template>
	<fieldset class="ldap-wizard__groups">
		{{ t('user_ldap', 'Groups meeting these criteria are available in {instanceName}:', {instanceName}) }}

		<div class="ldap-wizard__groups__line ldap-wizard__groups__filter-selection">
			<NcSelect v-model="ldapConfig.ldapGroupFilterObjectclass"
				class="ldap-wizard__groups__group-filter-groups__select"
				:options="['TODO']"
				:disable="allowUserFilterGroupsSelection"
				:input-label="t('user_ldap', 'Only these object classes:')"
				:multiple="true" />

			<!-- <input type="text" class="ldapManyGroupsSupport ldapManyGroupsSearch hidden" placeholder="t('user_ldap', 'Search groups')"> -->
			<NcSelect v-model="ldapConfig.ldapGroupFilterObjectclass"
				class="ldap-wizard__groups__group-filter-groups__select"
				:options="['TODO']"
				:disable="allowUserFilterGroupsSelection"
				:input-label="t('user_ldap', 'Only from these groups:')"
				:multiple="true" />
		</div>

		<!-- TODO -->
		<div class="ldap-wizard__groups__line">
			<p class="ldapManyGroupsSupport hidden">
				<select class="ldapGroupList ldapGroupListAvailable"
					:multiple="true"
					aria-describedby="ldapGroupListAvailable_instructions"
					title="t('user_ldap', 'Available groups')" />
			</p>
			<p id="ldapGroupListAvailable_instructions" class="hidden-visually">
				{{ t('user_ldap', 'Available groups') }}
			</p>

			<span>
				<NcButton class="ldapGroupListSelect">&gt;</NcButton><br>
				<NcButton class="ldapGroupListDeselect">&lt;</NcButton>
			</span>

			<select class="ldapGroupList ldapGroupListSelected"
				:multiple="true"
				aria-describedby="ldapGroupListSelected_instructions"
				title="t('user_ldap', 'Selected groups')" />
			<p id="ldapGroupListSelected_instructions" class="hidden-visually">
				{{ t('user_ldap', 'Selected groups') }}
			</p>
		</div>

		<div class="ldap-wizard__groups__line ldap-wizard__groups__groups-filter">
			<NcCheckboxRadioSwitch :checked.sync="editGroupsFilter">
				{{ t('user_name', 'Edit LDAP Query') }}
			</NcCheckboxRadioSwitch>

			<div v-if="!editGroupsFilter">
				<label>{{ t('user_name', 'LDAP Filter:') }}</label>
				<span>{{ ldapConfig.ldapGroupFilter }}</span>
			</div>
			<div v-else>
				<NcTextArea :value.sync="ldapConfig.ldapGroupFilter"
					:placeholder="t('user_name', 'Edit LDAP Query')"
					:helper-text="t('user_name', 'The filter specifies which LDAP groups shall have access to the {instanceName} instance.', {instanceName})" />
			</div>
		</div>

		<div class="ldap-wizard__groups__line ldap-wizard__groups__groups-count-check">
			<NcButton @click="getGroupsCount">
				{{ t('user_ldap', 'Verify settings and count the groups') }}
			</NcButton>

			<span v-if="groupsCount !== undefined">{{ t('user_ldap', "Groups count: {groupsCount}", { groupsCount }) }}</span>
		</div>
	</fieldset>
</template>

<script lang="ts" setup>
import { defineProps, computed, ref } from 'vue'

import { t } from '@nextcloud/l10n'
import { NcButton, NcTextArea, NcCheckboxRadioSwitch, NcSelect } from '@nextcloud/vue'

import { useLDAPConfigStore } from '../../store/config'

const ldapConfigStore = useLDAPConfigStore()

const { ldapConfigId } = defineProps({
	ldapConfigId: {
		type: String,
		required: true,
	},
})

const ldapConfig = computed(() => ldapConfigStore.ldapConfigs[ldapConfigId])

const instanceName = 'TODO'

const groupsCount = ref<number>(-1)
const editGroupsFilter = ref(false)
const allowUserFilterGroupsSelection = ref(false)

/**
 *
 */
async function getGroupsCount() {
	groupsCount.value++ // TODO: Implement
}
</script>
<style lang="scss" scoped>
.ldap-wizard__groups {
	display: flex;
	flex-direction: column;
	gap: 16px;

	&__line {
		display: flex;
		align-items: start;
	}

	&__filter-selection {
		flex-direction: column;
	}

	&__groups-filter {
		display: flex;
		flex-direction: column;
	}

	&__groups-count-check {
		display: flex;
		align-items: center;
		gap: 16px;
	}
}
</style>
