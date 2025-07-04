/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import { User } from '@nextcloud/cypress'

const testUser = new User('css-test-user', 'password123')

/**
 * These tests specifically validate the CSS changes from PR #53788
 * which adds max-width constraints to prevent profile distortion
 */
describe('Profile CSS validation for PR #53788', () => {
	before(() => {
		cy.createUser(testUser, {
			email: 'extremely.long.email.address.for.css.validation@very-long-domain.example.com',
			displayName: 'CSS Validation Test User'
		})
	})
	
	after(() => {
		cy.login(new User('admin', 'admin'))
		cy.deleteUser(testUser)
	})
	
	beforeEach(() => {
		cy.login(testUser)
		cy.visit(`/u/${testUser.userId}`)
		cy.get('.profile').should('be.visible')
	})
	
	it('Should apply max-width: 300px to .user-actions container', () => {
		// This is the main fix from the PR
		cy.get('.user-actions')
			.should('have.css', 'max-width', '300px')
			.and('have.css', 'display', 'flex')
			.and('have.css', 'flex-direction', 'column')
	})
	
	it('Should apply max-width: 100% to .user-actions__primary', () => {
		// This ensures the primary action doesn't exceed its container
		cy.get('.user-actions__primary')
			.should('have.css', 'max-width', '100%')
			.and('have.css', 'margin-left', 'auto')
			.and('have.css', 'margin-right', 'auto')
	})
	
	it('Should maintain proper spacing and gaps', () => {
		// Verify the gap between action items
		cy.get('.user-actions')
			.should('have.css', 'gap', '8px 0px')
			.and('have.css', 'margin-top', '20px')
	})
	
	it('Should properly style other actions', () => {
		cy.get('.user-actions__other')
			.should('have.css', 'display', 'flex')
			.should('have.css', 'justify-content', 'center')
			.should('have.css', 'gap', '0px 4px')
		
		// Check icon styling
		cy.get('.user-actions__other__icon').each(($icon) => {
			cy.wrap($icon)
				.should('have.css', 'width', '20px')
				.should('have.css', 'height', '20px')
				.should('have.css', 'margin', '12px')
		})
	})
	
	it('Should prevent layout distortion with long content', () => {
		// Get the user actions container width
		cy.get('.user-actions').then(($actions) => {
			const actionsWidth = $actions.outerWidth()
			
			// Should not exceed 300px even with long content
			expect(actionsWidth).to.be.at.most(300)
			
			// Primary action should fit within container
			cy.get('.user-actions__primary').then(($primary) => {
				const primaryWidth = $primary.outerWidth()
				expect(primaryWidth).to.be.at.most(actionsWidth)
			})
		})
		
		// Verify no horizontal scrollbar appears
		cy.window().then((win) => {
			const hasHorizontalScroll = win.document.body.scrollWidth > win.document.body.clientWidth
			expect(hasHorizontalScroll).to.be.false
		})
	})
	
	it('Should maintain profile blocks width of 640px on desktop', () => {
		cy.viewport(1920, 1080)
		
		cy.get('.profile__blocks')
			.should('have.css', 'width', '640px')
			.and('have.css', 'display', 'grid')
	})
	
	it('Should adapt profile blocks width on mobile', () => {
		cy.viewport('iphone-x')
		
		cy.get('.profile__blocks').then(($blocks) => {
			const width = parseInt($blocks.css('width'))
			// On mobile, should not have fixed 640px width
			expect(width).to.be.lessThan(640)
		})
		
		// But user actions should still have max-width constraint
		cy.get('.user-actions')
			.should('have.css', 'max-width', '300px')
	})
	
	it('Should handle CSS specificity correctly', () => {
		// Ensure the new CSS rules have proper specificity to override defaults
		cy.get('.user-actions').then(($el) => {
			// Get computed styles
			const maxWidth = window.getComputedStyle($el[0]).maxWidth
			const display = window.getComputedStyle($el[0]).display
			const flexDirection = window.getComputedStyle($el[0]).flexDirection
			
			// Verify computed values match expected CSS
			expect(maxWidth).to.equal('300px')
			expect(display).to.equal('flex')
			expect(flexDirection).to.equal('column')
		})
		
		cy.get('.user-actions__primary').then(($el) => {
			const maxWidth = window.getComputedStyle($el[0]).maxWidth
			// 100% should compute to actual pixels
			expect(maxWidth).to.match(/^\d+(\.\d+)?px$/)
		})
	})
	
	it('Should not break existing profile functionality', () => {
		// Ensure profile header is sticky
		cy.get('.profile__header')
			.should('have.css', 'position', 'sticky')
			.and('have.css', 'top', '-40px')
		
		// Ensure sidebar is sticky
		cy.get('.profile__sidebar')
			.should('have.css', 'position', 'sticky')
			.and('have.css', 'top', '0px')
		
		// Verify avatar is properly displayed
		cy.get('.profile__sidebar .avatar')
			.should('be.visible')
			.and('have.css', 'display', 'block')
	})
})

describe('Visual regression prevention', () => {
	const users = [
		{ id: 'no-email', email: undefined },
		{ id: 'short-email', email: 'a@b.c' },
		{ id: 'normal-email', email: 'user@example.com' },
		{ id: 'long-email', email: 'very.long.email.address@extremely-long-domain-name.co.uk' },
		{ id: 'special-chars', email: 'user+tag@sub.domain.example.com' }
	]
	
	before(() => {
		users.forEach(({ id, email }) => {
			const user = new User(id, 'password123')
			cy.createUser(user, email ? { email } : {})
		})
	})
	
	after(() => {
		const admin = new User('admin', 'admin')
		cy.login(admin)
		users.forEach(({ id }) => {
			cy.deleteUser(new User(id, 'password123'))
		})
	})
	
	users.forEach(({ id, email }) => {
		it(`Should render correctly for user with ${id}`, () => {
			const user = new User(id, 'password123')
			cy.login(user)
			cy.visit(`/u/${id}`)
			
			// Wait for profile to load
			cy.get('.profile').should('be.visible')
			
			// Verify layout constraints are applied
			cy.get('.user-actions').should('have.css', 'max-width', '300px')
			cy.get('.user-actions__primary').should('have.css', 'max-width', '100%')
			
			// Take screenshot for visual comparison
			cy.screenshot(`profile-visual-${id}`)
			
			// Verify no overflow
			cy.get('.user-actions__primary').then(($el) => {
				if ($el.length > 0) {
					const element = $el[0]
					const isOverflowing = element.scrollWidth > element.clientWidth
					expect(isOverflowing).to.be.false
				}
			})
		})
	})
})