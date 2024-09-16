<!--
 - SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 - SPDX-License-Identifier: AGPL-3.0-or-later
 -->
<template>
	<div>
		TODO: Wizard Controls: {{ ldapConfig.ldapHost }}
	</div>
</template>

<script lang="ts" setup>
import { defineProps, computed } from 'vue'

import { useLDAPConfigStore } from '../store/config'

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
</style>
