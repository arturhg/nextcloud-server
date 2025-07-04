# Profile E2E Tests

This directory contains end-to-end tests for the Nextcloud profile page functionality.

## Test Files

### profile-pr-53788.cy.ts
Main test file for PR #53788 which fixes profile layout issues when displaying long email addresses.
- Tests max-width constraints on user actions
- Validates layout doesn't break with long content
- Checks responsive behavior

### profile-layout.cy.ts
Comprehensive layout tests covering:
- Various email lengths
- Responsive design across different viewports
- Edge cases (RTL text, no email, long display names)
- Performance testing

### profile-css-validation.cy.ts
Specific CSS validation tests for PR #53788:
- Validates the exact CSS properties added
- Checks computed styles
- Visual regression prevention
- Ensures no existing functionality is broken

### profileUtils.ts
Utility functions for profile testing:
- Navigation helpers
- Element selectors
- Layout validation functions
- Responsive testing utilities

## Running the Tests

```bash
# Run all profile tests
npm run cypress:run -- --spec "cypress/e2e/profile/**/*.cy.ts"

# Run specific test file
npm run cypress:run -- --spec "cypress/e2e/profile/profile-pr-53788.cy.ts"

# Run in interactive mode
npm run cypress:open
```

## Test Coverage

The tests cover:
1. **Layout Constraints**: Verify max-width is properly applied to prevent distortion
2. **Responsive Design**: Test across desktop, tablet, and mobile viewports
3. **Edge Cases**: Long emails, no emails, RTL text, special characters
4. **Visual Regression**: Screenshots for visual comparison
5. **Performance**: Page load time and layout stability

## PR #53788 Context

The PR addresses a layout issue where long email addresses in the primary action button
could cause the profile page to look distorted. The fix adds:
- `max-width: 300px` to `.user-actions` container
- `max-width: 100%` to `.user-actions__primary` button

These tests ensure the fix works correctly and doesn't introduce regressions.