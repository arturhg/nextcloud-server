/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import { User } from '@nextcloud/cypress'
import {
	visitProfile,
	getUserActions,
	getPrimaryAction,
	getOtherActions,
	verifyNoOverflow,
	verifyTextTruncation,
	verifyProfileStructure,
	testResponsiveLayout
} from './profileUtils'

const admin = new User('admin', 'admin')

// Test users with different email lengths
const users = [
	{
		user: new User('short-email', 'password123'),
		email: 'short@ex.com',
		displayName: 'Short Email User'
	},
	{
		user: new User('medium-email', 'password123'),
		email: 'medium.length.email@example.com',
		displayName: 'Medium Email User'
	},
	{
		user: new User('long-email', 'password123'),
		email: 'very.long.email.address.that.should.not.break.the.layout@extremely-long-domain-name.example.com',
		displayName: 'Long Email User'
	}
]

describe('Profile layout with various email lengths', () => {
	before(() => {
		// Create all test users
		users.forEach(({ user, email, displayName }) => {
			cy.createUser(user, { email, displayName })
		})
	})

	after(() => {
		// Clean up all test users
		cy.login(admin)
		users.forEach(({ user }) => {
			cy.deleteUser(user)
		})
	})

	users.forEach(({ user, email, displayName }) => {
		it(`Should handle ${user.userId} profile layout correctly`, () => {
			cy.login(user)
			visitProfile(user.userId)
			
			// Verify profile structure
			verifyProfileStructure()
			
			// Check user actions container
			getUserActions()
				.should('exist')
				.and('be.visible')
				.and('have.css', 'max-width', '300px')
			
			// Verify primary action doesn't overflow
			getPrimaryAction().then(($primary) => {
				if ($primary.length > 0) {
					verifyNoOverflow('.user-actions__primary')
					
					// Check if email is displayed and properly handled
					const text = $primary.text()
					if (text.includes('@')) {
						// For long emails, verify text truncation
						if (email.length > 30) {
							verifyTextTruncation('.user-actions__primary')
						}
					}
				}
			})
			
			// Take screenshot for visual comparison
			cy.screenshot(`profile-${user.userId}`)
		})
	})
})

describe('Profile responsive design tests', () => {
	const testUser = new User('responsive-test', 'password123')
	
	before(() => {
		cy.createUser(testUser, {
			email: 'responsive.test.user.with.long.email@example.com',
			displayName: 'Responsive Test User'
		})
	})
	
	after(() => {
		cy.login(admin)
		cy.deleteUser(testUser)
	})
	
	it('Should maintain layout integrity across different viewports', () => {
		cy.login(testUser)
		visitProfile(testUser.userId)
		
		const viewports = [
			{ name: 'Desktop', width: 1920, height: 1080 },
			{ name: 'Laptop', width: 1366, height: 768 },
			{ name: 'Tablet', width: 768, height: 1024 },
			{ name: 'Mobile', width: 375, height: 667 }
		]
		
		testResponsiveLayout(viewports)
	})
	
	it('Should adapt user actions layout on mobile', () => {
		cy.login(testUser)
		cy.viewport('iphone-x')
		visitProfile(testUser.userId)
		
		// User actions should still be constrained
		getUserActions()
			.should('be.visible')
			.and('have.css', 'max-width', '300px')
		
		// Primary action should be centered and not overflow
		getPrimaryAction()
			.should('have.css', 'margin-left', 'auto')
			.should('have.css', 'margin-right', 'auto')
		
		verifyNoOverflow('.user-actions__primary')
		
		// Other actions should remain centered
		getOtherActions()
			.should('have.css', 'display', 'flex')
			.should('have.css', 'justify-content', 'center')
	})
})

describe('Profile edge cases and stress tests', () => {
	const edgeCaseUser = new User('edge-case-user', 'password123')
	
	beforeEach(() => {
		cy.listUsers().then((users) => {
			if ((users as string[]).includes(edgeCaseUser.userId)) {
				cy.deleteUser(edgeCaseUser)
			}
		})
	})
	
	afterEach(() => {
		cy.login(admin)
		cy.deleteUser(edgeCaseUser).catch(() => {
			// Ignore if user doesn't exist
		})
	})
	
	it('Should handle profile with no email gracefully', () => {
		cy.createUser(edgeCaseUser, {
			displayName: 'User Without Email'
		})
		
		cy.login(edgeCaseUser)
		visitProfile(edgeCaseUser.userId)
		
		// Profile should still render properly
		verifyProfileStructure()
		
		// User actions should be present even without email
		getUserActions().should('exist')
	})
	
	it('Should handle very long display names', () => {
		const longDisplayName = 'User With Extremely Long Display Name '.repeat(5)
		
		cy.createUser(edgeCaseUser, {
			email: 'test@example.com',
			displayName: longDisplayName
		})
		
		cy.login(edgeCaseUser)
		visitProfile(edgeCaseUser.userId)
		
		// Display name container should not overflow
		verifyNoOverflow('.profile__header__container__displayname')
		
		// Profile structure should remain intact
		verifyProfileStructure()
	})
	
	it('Should handle RTL text in profile', () => {
		const rtlDisplayName = 'مستخدم اختبار'
		const rtlEmail = 'test@مثال.com'
		
		cy.createUser(edgeCaseUser, {
			email: rtlEmail,
			displayName: rtlDisplayName
		})
		
		cy.login(edgeCaseUser)
		visitProfile(edgeCaseUser.userId)
		
		// Profile should render RTL content properly
		verifyProfileStructure()
		
		// User actions should maintain proper constraints
		getUserActions()
			.should('have.css', 'max-width', '300px')
		
		verifyNoOverflow('.user-actions')
		verifyNoOverflow('.user-actions__primary')
	})
})

describe('Profile performance tests', () => {
	const perfUser = new User('perf-test-user', 'password123')
	
	before(() => {
		cy.createUser(perfUser, {
			email: 'performance.test@example.com',
			displayName: 'Performance Test User'
		})
	})
	
	after(() => {
		cy.login(admin)
		cy.deleteUser(perfUser)
	})
	
	it('Should load profile page efficiently', () => {
		cy.login(perfUser)
		
		// Measure page load time
		cy.visit(`/u/${perfUser.userId}`, {
			onBeforeLoad: (win) => {
				win.performance.mark('profile-start')
			},
			onLoad: (win) => {
				win.performance.mark('profile-end')
				win.performance.measure('profile-load', 'profile-start', 'profile-end')
				
				const measure = win.performance.getEntriesByName('profile-load')[0]
				expect(measure.duration).to.be.lessThan(3000) // Should load within 3 seconds
			}
		})
		
		// Verify all elements render without layout shifts
		cy.get('.profile').should('be.visible')
		cy.get('.user-actions').should('be.visible')
		
		// Check for any layout shifts after load
		cy.wait(1000) // Wait for any async operations
		
		// Verify positions haven't changed
		getUserActions().then(($el) => {
			const initialPosition = $el.offset()
			cy.wait(500)
			getUserActions().then(($el2) => {
				const finalPosition = $el2.offset()
				expect(initialPosition.top).to.equal(finalPosition.top)
				expect(initialPosition.left).to.equal(finalPosition.left)
			})
		})
	})
})