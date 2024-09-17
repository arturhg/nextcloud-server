		/**
		 * starts a configuration test on the Nextcloud server
		 */
		requestConfigurationTest: function() {
			var url = OC.generateUrl('apps/user_ldap/ajax/testConfiguration.php');
			var params = OC.buildQueryString({ldap_serverconfig_chooser: this.configID});
			var model = this;
			$.post(url, params, function(result) { model._processTestResult(model, result) });
			//TODO: make sure only one test is running at a time
		},