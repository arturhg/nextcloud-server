/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import { User } from '@nextcloud/cypress'

const admin = new User('admin', 'admin')
const testUser = new User('test-user-pr-53788', 'password123')

describe('Profile page layout fix (PR #53788)', () => {
	beforeEach(() => {
		// Clean up test user if exists
		cy.listUsers().then((users) => {
			if ((users as string[]).includes(testUser.userId)) {
				cy.deleteUser(testUser)
			}
		})
		
		// Create test user with a long email address
		cy.createUser(testUser, {
			email: 'very.long.email.address.that.could.cause.layout.issues@example.com',
			displayName: 'Test User PR 53788',
		})
		
		cy.login(testUser)
	})

	afterEach(() => {
		// Clean up test user
		cy.logout()
		cy.login(admin)
		cy.deleteUser(testUser)
	})

	it('Should display user actions with proper max-width', () => {
		// Visit the profile page
		cy.visit(`/u/${testUser.userId}`)
		
		// Wait for the profile page to load
		cy.get('.profile').should('be.visible')
		
		// Check that user actions container exists
		cy.get('.user-actions').should('exist').and('be.visible')
		
		// Verify that the user actions container has max-width constraint
		cy.get('.user-actions').should('have.css', 'max-width').and('match', /300px/)
		
		// Check that primary action button exists and has max-width
		cy.get('.user-actions__primary').should('exist').and('be.visible')
		cy.get('.user-actions__primary').should('have.css', 'max-width').and('match', /100%/)
		
		// Verify the primary action button doesn't overflow
		cy.get('.user-actions__primary').then(($el) => {
			const containerWidth = $el.parent().width()
			const elementWidth = $el.outerWidth()
			expect(elementWidth).to.be.at.most(containerWidth)
		})
	})

	it('Should handle long email addresses without distorting layout', () => {
		// Visit the profile page
		cy.visit(`/u/${testUser.userId}`)
		
		// Wait for the profile page to load
		cy.get('.profile').should('be.visible')
		
		// Check if email action button exists (if visible on profile)
		cy.get('.user-actions__primary').then(($button) => {
			// Get the button text
			const buttonText = $button.text().trim()
			
			// If the button contains an email, verify it's properly truncated/handled
			if (buttonText.includes('@')) {
				// Check that the button width doesn't exceed its container
				const buttonWidth = $button.outerWidth()
				const containerWidth = $button.parent().width()
				expect(buttonWidth).to.be.at.most(containerWidth)
				
				// Verify text overflow is handled properly
				cy.wrap($button).should('have.css', 'overflow').and('not.equal', 'visible')
			}
		})
		
		// Verify the overall profile layout is not distorted
		cy.get('.profile__header').should('be.visible')
		cy.get('.profile__content').should('be.visible')
		cy.get('.profile__sidebar').should('be.visible')
		
		// Check that profile blocks maintain proper width
		cy.get('.profile__blocks').should('have.css', 'width').and('match', /640px/)
	})

	it('Should maintain responsive layout on mobile viewport', () => {
		// Set mobile viewport
		cy.viewport('iphone-x')
		
		// Visit the profile page
		cy.visit(`/u/${testUser.userId}`)
		
		// Wait for the profile page to load
		cy.get('.profile').should('be.visible')
		
		// On mobile, check that user actions still have proper constraints
		cy.get('.user-actions').should('exist').and('be.visible')
		
		// Verify max-width is still applied
		cy.get('.user-actions').should('have.css', 'max-width').and('match', /300px/)
		
		// Check that the layout adapts properly for mobile
		cy.get('.profile__blocks').then(($blocks) => {
			const width = $blocks.width()
			// On mobile, blocks should not have fixed 640px width
			expect(width).to.be.lessThan(640)
		})
		
		// Verify no horizontal overflow
		cy.get('body').then(($body) => {
			const bodyWidth = $body.width()
			const scrollWidth = $body[0].scrollWidth
			expect(scrollWidth).to.equal(bodyWidth)
		})
	})

	it('Should display other user actions properly', () => {
		// Visit the profile page
		cy.visit(`/u/${testUser.userId}`)
		
		// Wait for the profile page to load
		cy.get('.profile').should('be.visible')
		
		// Check if other actions exist
		cy.get('.user-actions__other').should('exist')
		
		// Verify other actions layout
		cy.get('.user-actions__other').should('have.css', 'display', 'flex')
		cy.get('.user-actions__other').should('have.css', 'justify-content', 'center')
		
		// Check icon sizing in other actions
		cy.get('.user-actions__other__icon').each(($icon) => {
			cy.wrap($icon).should('have.css', 'width', '20px')
			cy.wrap($icon).should('have.css', 'height', '20px')
		})
	})
})

describe('Profile page visual regression tests', () => {
	before(() => {
		// Create a user with various profile configurations
		cy.createUser(testUser, {
			email: 'extremely.long.email.address.for.testing.layout.issues@very-long-domain-name.example.com',
			displayName: 'Test User with Very Long Display Name for PR 53788',
		})
	})

	after(() => {
		// Clean up
		cy.login(admin)
		cy.deleteUser(testUser)
	})

	it('Should not break layout with extreme content lengths', () => {
		cy.login(testUser)
		cy.visit(`/u/${testUser.userId}`)
		
		// Wait for profile to load
		cy.get('.profile').should('be.visible')
		
		// Take a screenshot for visual regression
		cy.screenshot('profile-with-long-content')
		
		// Verify no elements overflow their containers
		cy.get('.profile__header__container__displayname').then(($el) => {
			const containerWidth = $el.parent().width()
			const elementWidth = $el.outerWidth()
			expect(elementWidth).to.be.at.most(containerWidth)
		})
		
		// Check user actions don't overflow
		cy.get('.user-actions').then(($el) => {
			const parentWidth = $el.parent().width()
			const elementWidth = $el.outerWidth()
			expect(elementWidth).to.be.at.most(parentWidth)
			expect(elementWidth).to.be.at.most(300) // max-width from CSS
		})
	})
})