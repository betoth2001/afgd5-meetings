# afgd5-meetings Wordpress Plugin
This plugin uses several custom post meta fields to sync a post with a specified Google calendar.  The fields are managed by the Advanced Custom Fields plugin.  Those meta fields are
* info - a list of checkboxes
* location - a Google map location
* day - a select list limited to only one day
* start_time - a time field
* end_time - a time field
* google\_event\_id - unique identifier for the most up to date recurring event

The events recur with weekly frequency.  Changes to a post are used to update future occurences without changing the id, while creating a past recurring event with the old information.  This should mimic the functionality of selecting "Following events" in Google calendar when editing a recurring event.

## Requested Features
- [ ] Avoid attempting to create recurring event that would contain no instances if new\_start - old\_start less than 1 week and does not contain day of week
- [*] Handle error codes returned by google services
