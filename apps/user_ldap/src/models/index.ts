/**
 * SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

export type LDAPConfig = {
	ldapHost: string // Example: ldaps://my.ldap.server</ldapHost>
	ldapPort: string // Example: 7770
	ldapBackupHost: string
	ldapBackupPort: string
	ldapBase: string // Example: ou=small,dc=my,dc=ldap,dc=server
	ldapBaseUsers: string // Example: ou=users,ou=small,dc=my,dc=ldap,dc=server
	ldapBaseGroups: string // Example: ou=small,dc=my,dc=ldap,dc=server
	ldapAgentName: string // Example: cn=root,dc=my,dc=ldap,dc=server
	ldapAgentPassword: string // Example: clearTextWithShowPassword=1
	ldapTLS: string // Example: 1
	turnOffCertCheck: string // Example: 0
	ldapIgnoreNamingRules: string // Example: >
	ldapUserDisplayName: string // Example: displayname
	ldapUserDisplayName2: string // Example: uid
	ldapUserFilterObjectclass: string // Example: inetOrgPerson
	ldapUserFilterGroups: string
	ldapUserFilter: string // Example: (&amp;(objectclass=nextcloudUser)(nextcloudEnabled=TRUE))
	ldapUserFilterMode: string // Example: 1
	ldapGroupFilter: string // Example: (&amp;(|(objectclass=nextcloudGroup)))
	ldapGroupFilterMode: string // Example: 0
	ldapGroupFilterObjectclass: string // Example: nextcloudGroup
	ldapGroupFilterGroups: string
	ldapGroupDisplayName: string // Example: cn
	ldapGroupMemberAssocAttr: string // Example: memberUid
	ldapLoginFilter: string // Example: (&amp;(|(objectclass=inetOrgPerson))(uid=%uid))
	ldapLoginFilterMode: string // Example: 0
	ldapLoginFilterEmail: string // Example: 0
	ldapLoginFilterUsername: string // Example: 1
	ldapLoginFilterAttributes: string
	ldapQuotaAttribute: string
	ldapQuotaDefault: string
	ldapEmailAttribute: string // Example: mail
	ldapCacheTTL: string // Example: 20
	ldapUuidUserAttribute: string // Example: auto
	ldapUuidGroupAttribute: string // Example: auto
	ldapOverrideMainServer: string
	ldapConfigurationActive: string // Example: 1
	ldapAttributesForUserSearch: string // Example: uid;sn;givenname
	ldapAttributesForGroupSearch: string
	ldapExperiencedAdmin: string // Example: 0
	homeFolderNamingRule: string
	hasMemberOfFilterSupport: string
	useMemberOfToDetectMembership: string // Example: 1
	ldapExpertUsernameAttr: string // Example: uid
	ldapExpertUUIDUserAttr: string // Example: uid
	ldapExpertUUIDGroupAttr: string
	lastJpegPhotoLookup: string // Example: 0
	ldapNestedGroups: string // Example: 0
	ldapPagingSize: string // Example: 500
	turnOnPasswordChange: string // Example: 1
	ldapDynamicGroupMemberURL: string
	markRemnantsAsDisabled: string // Example: 1
	ldapDefaultPPolicyDN: string
	ldapExtStorageHomeAttribute: string
	ldapAttributePhone: string
	ldapAttributeWebsite: string
	ldapAttributeAddress: string
	ldapAttributeTwitter: string
	ldapAttributeFediverse: string
	ldapAttributeOrganisation: string
	ldapAttributeRole: string
	ldapAttributeHeadline: string
	ldapAttributeBiography: string
	ldapAttributeBirthDate: string
}
