[![](https://img.shields.io/packagist/v/inspiredminds/contao-member-invites.svg)](https://packagist.org/packages/inspiredminds/contao-member-invites)
[![](https://img.shields.io/packagist/dt/inspiredminds/contao-member-invites.svg)](https://packagist.org/packages/inspiredminds/contao-member-invites)

Contao Member Invites
=====================

This Contao extension allows members of your site to send and review invites.


## Usage

The following steps need to be done to use this extension:

1. Create notifications for invites, invite requests and new registrations.
2. Create a page where members can send invites to other people.
3. Create a page where recipients of invites can accept the invite and register.
4. Create a _Registration_ for invited people to register.
5. Create a _Member invite form_ module for sending invites.
6. Create a _Member invite accept_ module for accepting invites.

### Notifications

There are two notification types provided by this extension:

#### Member invite

This notification will be selected in the _Member invite form_ module and is sent when an invite is created by a member in the front end. The following simple tokens are available within the notification:

* `##member_*##`: any information about the member sending the invite.
* `##invite_*##`: any information about the invite (i.e. the recipient). This includes `##invite_firstname##`, `##invite_lastname##`, `##invite_email##` and `##invite_message##` by default.
* `##invite_link##`: this will be replaced with the unique invite link, where the recpient can accept the invite and register.

The notification could look like this for example:

<img src="https://github.com/inspiredminds/contao-member-invites/raw/main/notification-member-invite.png" width="442" alt="Member invite notification">

#### Request another invitation

This notification will be selected in the _Member invite accept_ module and is sent when an invite has expired and the recpient requests another invitation in the front end. The following simple tokens are available within the notification:

* `##member_*##`: Any information about the member that originally sent the invite.
* `##invite_*##`: Any information about the invite (i.e. the recipient). This includes `##invite_firstname##`, `##invite_lastname##`, `##invite_email##` and `##invite_message##` by default.
* `##resend_link##`: This will be replaced with the URL to the page containing the _Member invite form_ module, where the sender can send the invite again.

The notification could look like this for example:

<img src="https://github.com/inspiredminds/contao-member-invites/raw/main/notification-request-another-invitation.png" width="442" alt="Request another invitation notification">

#### Member registration

This notification is not part of this extension, but this extension provides an additional token that can be used for the notification that is sent when a new member registers on the site:

* `##backend_link##`: This provides a link to the edit view of the newly registered member in the backend. This can be used for notifications about new registrations to the site's administrator.

### Modules

#### Member invite form

This module will display a form where a member can send an invitation via email to another person to register on the current website. The module takes three settings:

* __Notification__: The _Member invite_ notification mentioned above.
* __Invite expiration__: Determines how long an invite link should stay valid.
* __Redirect page__: This is the page where the recipient of the invite will be sent to. It should include a _Member invite accept_ module (see below).

<img src="https://github.com/inspiredminds/contao-member-invites/raw/main/backend-member-invite-form.png" width="367" alt="Member invite form module configuration">

<img src="https://github.com/inspiredminds/contao-member-invites/raw/main/frontend-member-invite-form.png" width="343" alt="Member invite form module front end">

#### Member invite accept

This module will either display the selected registration form or a button where the recipient can request another invitation link, if the invite has expired. The module takes three settings:

* __Notification__: The _Request another invitation_ notification mentioned above.
* __Registration module__: The registration module to be used for valid invite links.
* __Redirect page__: This is the page where the sender of the invite will be sent to if a recipient of an invite requests another invitation, in case the invite expired. It should include a _Member invite_ module (see above).

<img src="https://github.com/inspiredminds/contao-member-invites/raw/main/backend-member-invite-accept.png" width="367" alt="Member invite accept module configuration">

If the invite link is valid, the registration module will be shown, with the information of the invite pre-filled in the respective form elements:

<img src="https://github.com/inspiredminds/contao-member-invites/raw/main/frontend-member-invite-accept-2.png" width="343" alt="Member invite accept module registration form">

If the invite link expired, a button to request another invitation will be shown instead:

<img src="https://github.com/inspiredminds/contao-member-invites/raw/main/frontend-member-invite-accept-3.png" width="343" alt="Member invite accept module registration form">

If the invite link is otherwise invalid, a message will be shown:

<img src="https://github.com/inspiredminds/contao-member-invites/raw/main/frontend-member-invite-accept-1.png" width="343" alt="Member invite accept module registration form">

#### Member invite table

This module will display all the invites sent by the currently logged in member. It will include a link to send an invite again. The link will either point to the current page or the redirect page.

#### Member invite overview

This module will dispaly all the invites in the system in the front end.

## Attributions

Development of this extension was funded by the [austrian society for artificial intelligence (ASAI)](https://www.asai.ac.at/) with the Austrian [Federal Ministry for Climate Action, Environment, Energy, Mobility, Innovation and Technology (BMK)](https://www.bmk.gv.at/) as the public funding body.
