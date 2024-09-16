<!--
 - SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 - SPDX-License-Identifier: AGPL-3.0-or-later
 -->
<template>
	<fieldset class="ldap-wizard__users">
		{{ t('user_name', 'Listing and searching for users is constrained by these criteria:') }}

		<div class="ldap-wizard__users__line ldap-wizard__users__user-filter-object-class">
			<NcSelect v-model="ldapConfig.ldapUserFilterObjectclass"
				class="ldap-wizard__users__user-filter-object-class__select"
				:disable="editUserFilter"
				:options="['TODO']"
				:input-label="t('user_name', 'Only these object classes:')"
				:multiple="true" />
			{{ t('user_name', 'The most common object classes for users are organizationalPerson, person, user, and inetOrgPerson. If you are not sure which object class to select, please consult your directory admin.') }}</span>
		</div>

		<div class="ldap-wizard__users__line ldap-wizard__users__user-filter-groups">
			<div>
				{{ t('user_name', 'Only from these groups:') }}
			</div>

			<!-- <input type="text" class="ldapManyGroupsSupport ldapManyGroupsSearch hidden" > -->
			<!-- <NcTextField :disable="editUserFilter"
				:value.sync="ldapConfig.ldapUserFilterGroups"
				:placeholder="t('user_name', 'Search groups')"
				autocomplete="off" /> -->

			<NcSelect v-model="ldapConfig.ldapUserFilterGroups"
				class="ldap-wizard__users__user-filter-groups__select"
				:options="['TODO']"
				:disable="allowUserFilterGroupsSelection"
				:input-label="t('user_name', 'Only these object classes:')"
				:multiple="true" />
		</div>

		<!-- TODO -->
		<div class="ldap-wizard__users__line">
			<p class="ldapManyGroupsSupport hidden">
				<select class="ldapGroupList ldapGroupListAvailable"
					multiple="multiple"
					aria-describedby="ldapGroupListAvailable_instructions"
					:title="t('user_name', 'Available groups')" />
			</p>
			<p id="ldapGroupListAvailable_instructions" class="hidden-visually">
				{{ t('user_name', 'Available groups') }}
			</p>

			<span>
				<NcButton class="ldapGroupListSelect">&gt;</NcButton>
				<NcButton class="ldapGroupListDeselect">&lt;</NcButton>
			</span>

			<select class="ldapGroupList ldapGroupListSelected"
				multiple="multiple"
				aria-describedby="ldapGroupListSelected_instructions"
				:title="t('user_name', 'Selected groups')" />

			<p id="ldapGroupListSelected_instructions" class="hidden-visually">
				{{ t('user_name', 'Selected groups') }}
			</p>
		</div>

		<div class="ldap-wizard__users__line ldap-wizard__users__user-filter">
			<NcCheckboxRadioSwitch :checked.sync="editUserFilter">
				{{ t('user_name', 'Edit LDAP Query') }}
			</NcCheckboxRadioSwitch>

			<div v-if="!editUserFilter">
				<label>{{ t('user_name', 'LDAP Filter:') }}</label>
				<span>{{ ldapConfig.ldapUserListFilter }}</span>
			</div>
			<div v-else>
				<NcTextArea :value.sync="ldapConfig.ldapUserListFilter"
					:placeholder="t('user_name', 'Edit LDAP Query')"
					:helper-text="t('user_name', 'The filter specifies which LDAP users shall have access to the {instanceName} instance.', { instanceName })" />
			</div>
		</div>

		<div class="ldap-wizard__users__line ldap-wizard__users__user-count-check">
			<NcButton @click="getUserCount">
				{{ t('user_name', 'Verify settings and count users') }}
			</NcButton>

			<span v-if="userCount !== undefined">{{ t('user_ldap', "User count: {userCount}", { userCount }) }}</span>
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

const userCount = ref<number>(-1)

const editUserFilter = ref(false)
const allowUserFilterGroupsSelection = ref(true) // TODO
const instanceName = 'TODO'

/**
 *
 */
async function getUserCount() {
	userCount.value++ // TODO: Implement
}
</script>
<style lang="scss" scoped>
.ldap-wizard__users {
	display: flex;
	flex-direction: column;
	gap: 16px;

	&__line {
		display: flex;
		align-items: start;
	}

	&__user-filter-object-class {
		display: flex;
		gap: 16px;

		&__select {
			min-width: 50%;
			flex-grow: 1;
		}
	}

	&__user-filter-groups {
		display: flex;
		gap: 16px;

		&__select {
			min-width: 50%;
			flex-grow: 1;
		}
	}

	&__user-filter {
		display: flex;
		flex-direction: column;
	}

	&__user-count-check {
		display: flex;
		align-items: center;
		gap: 16px;
	}
}
</style>
