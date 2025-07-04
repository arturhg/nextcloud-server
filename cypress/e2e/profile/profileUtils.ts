/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

/**
 * Navigate to a user's profile page
 * @param userId The user ID to visit
 */
export function visitProfile(userId: string): void {
	cy.visit(`/u/${userId}`)
	cy.get('.profile').should('be.visible')
}

/**
 * Get the user actions container
 */
export function getUserActions() {
	return cy.get('.user-actions')
}

/**
 * Get the primary user action button
 */
export function getPrimaryAction() {
	return cy.get('.user-actions__primary')
}

/**
 * Get other user action buttons
 */
export function getOtherActions() {
	return cy.get('.user-actions__other')
}

/**
 * Verify that an element doesn't overflow its container
 * @param selector The CSS selector for the element to check
 */
export function verifyNoOverflow(selector: string): void {
	cy.get(selector).then(($el) => {
		const element = $el[0]
		const parent = element.parentElement
		
		if (parent) {
			const elementRect = element.getBoundingClientRect()
			const parentRect = parent.getBoundingClientRect()
			
			// Check horizontal overflow
			expect(elementRect.left).to.be.at.least(parentRect.left)
			expect(elementRect.right).to.be.at.most(parentRect.right)
		}
	})
}

/**
 * Check if text is properly truncated with ellipsis
 * @param selector The CSS selector for the element to check
 */
export function verifyTextTruncation(selector: string): void {
	cy.get(selector).should(($el) => {
		const element = $el[0]
		const isOverflowing = element.scrollWidth > element.clientWidth
		
		if (isOverflowing) {
			// If text overflows, it should have proper CSS to handle it
			const overflow = $el.css('overflow')
			const textOverflow = $el.css('text-overflow')
			const whiteSpace = $el.css('white-space')
			
			expect(overflow).to.match(/hidden|clip/)
			expect(textOverflow).to.equal('ellipsis')
			expect(whiteSpace).to.equal('nowrap')
		}
	})
}

/**
 * Set up a user with profile data for testing
 * @param user The user object
 * @param profileData Additional profile data
 */
export function setupUserProfile(user: any, profileData: {
	headline?: string
	biography?: string
	location?: string
	website?: string
	twitter?: string
	organisation?: string
	role?: string
}): void {
	// This would typically use API calls to set profile data
	// For now, we'll just log the intent
	cy.log(`Setting up profile for ${user.userId}`, profileData)
}

/**
 * Verify the profile page structure is intact
 */
export function verifyProfileStructure(): void {
	// Header should be visible
	cy.get('.profile__header').should('be.visible')
	
	// Sidebar with avatar should be visible
	cy.get('.profile__sidebar').should('be.visible')
	cy.get('.profile__sidebar .avatar').should('be.visible')
	
	// Content area should be visible
	cy.get('.profile__content').should('be.visible')
	
	// Profile blocks should be visible
	cy.get('.profile__blocks').should('be.visible')
}

/**
 * Check responsive behavior at different viewports
 * @param viewports Array of viewport configurations to test
 */
export function testResponsiveLayout(viewports: Array<{ name: string, width: number, height: number }>): void {
	viewports.forEach(viewport => {
		cy.viewport(viewport.width, viewport.height)
		cy.log(`Testing at ${viewport.name} (${viewport.width}x${viewport.height})`)
		
		// Verify no horizontal scroll
		cy.window().then((win) => {
			expect(win.document.body.scrollWidth).to.equal(win.document.body.clientWidth)
		})
		
		// Verify profile structure is maintained
		verifyProfileStructure()
		
		// Verify user actions are properly constrained
		getUserActions().should('have.css', 'max-width').and('match', /300px/)
	})
}