<?php

namespace ErikGall\Samsara;

use Saloon\Http\Connector;
use ErikGall\Samsara\Resource\Ifta;
use ErikGall\Samsara\Resource\Tags;
use ErikGall\Samsara\Resource\Trips;
use ErikGall\Samsara\Resource\Users;
use ErikGall\Samsara\Resource\Alerts;
use ErikGall\Samsara\Resource\Assets;
use ErikGall\Samsara\Resource\Idling;
use ErikGall\Samsara\Resource\Routes;
use ErikGall\Samsara\Resource\Safety;
use ErikGall\Samsara\Resource\Drivers;
use ErikGall\Samsara\Resource\Sensors;
use ErikGall\Samsara\Resource\BetaApis;
use ErikGall\Samsara\Resource\Coaching;
use ErikGall\Samsara\Resource\Contacts;
use ErikGall\Samsara\Resource\Gateways;
use ErikGall\Samsara\Resource\Messages;
use ErikGall\Samsara\Resource\Settings;
use ErikGall\Samsara\Resource\Trailers;
use ErikGall\Samsara\Resource\Vehicles;
use ErikGall\Samsara\Resource\Webhooks;
use ErikGall\Samsara\Resource\Addresses;
use ErikGall\Samsara\Resource\Documents;
use ErikGall\Samsara\Resource\Equipment;
use Saloon\Http\Auth\TokenAuthenticator;
use ErikGall\Samsara\Resource\Attributes;
use ErikGall\Samsara\Resource\Industrial;
use ErikGall\Samsara\Resource\LegacyApis;
use ErikGall\Samsara\Resource\Maintenance;
use ErikGall\Samsara\Resource\PreviewApis;
use ErikGall\Samsara\Resource\VehicleStats;
use ErikGall\Samsara\Resource\DriverQrCodes;
use ErikGall\Samsara\Resource\FuelAndEnergy;
use ErikGall\Samsara\Resource\HoursOfService;
use ErikGall\Samsara\Resource\MediaRetrievals;
use ErikGall\Samsara\Resource\LiveSharingLinks;
use ErikGall\Samsara\Resource\LocationAndSpeed;
use ErikGall\Samsara\Resource\OrganizationInfo;
use ErikGall\Samsara\Resource\TachographEuOnly;
use ErikGall\Samsara\Resource\VehicleLocations;
use ErikGall\Samsara\Resource\SpeedingIntervals;
use ErikGall\Samsara\Resource\TrailerAssignments;
use ErikGall\Samsara\Resource\DriverVehicleAssignments;
use ErikGall\Samsara\Resource\CarrierProposedAssignments;

/**
 * Samsara API.
 *
 * <style type="text/css">
 * n {
 *     padding: 1em;
 *     width: 100%;
 *     display: block;
 *     margin: 28px 0;
 * }
 * n.info {
 *     background-color: rgba(0, 51, 160, 0.1);
 * }
 * n.warning {
 *     background-color: #fdf6e3;
 * }
 * i:before {
 *     margin-right: 6px;
 * }
 * nh {
 *     font-size: 1.5rem;
 *     font-weight: 700;
 *     line-height: 1.1;
 *     display: block;
 * }
 * nb {
 *     margin-top: 10px;
 *     padding-left: 22px;
 *     display: block;
 * }
 * </style>
 *
 * # Overview
 *
 * <n class="info">
 * <nh>
 * <i class="fa fa-info-circle"></i>
 * Something new!
 * </nh>
 * <nb>
 * Welcome Samsara's new and improved API. Check out our FAQ [here](https://developers.samsara.com/docs/introducing-our-next-generation-api) to see what's changed and learn how to get started.<br>
 * <br>
 * Want to access the legacy API docs? You can find them [here](https://www.samsara.com/api-legacy).<br>
 * <br>
 * *Note: Because this is a new set of APIs, we have not transitioned all endpoints over to this standard. Endpoints that still use the legacy standards are indicated in the reference documentation. If you can't find an API that you're looking for, we encourage you to look for it in our [legacy API docs](https://www.samsara.com/api-legacy) as we continue to transition all endpoints over. Check back here for updates!*<br>
 * <br>
 * Submit your feedback [here](https://forms.gle/r4bs6HQshQAvBuwv6)!
 * </nb>
 * </n>
 *
 * Samsara provides API endpoints so that you can build powerful applications and custom solutions with sensor data. Samsara has endpoints available to track and analyze sensors, vehicles, and entire fleets.
 *
 * The Samsara API is a [RESTful API](https://en.wikipedia.org/wiki/Representational_state_transfer). It uses standard [HTTP](https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol) authentication, verbs, and response codes, and it returns [JSON](http://www.json.org/) response bodies. If you're familiar with what you can build with a REST API, then this will be your go-to API reference.
 *
 * Visit [developers.samsara.com](https://developers.samsara.com) to find getting started guides and an API overview.
 *
 * If you have any questions, please visit https://samsara.com/help.
 *
 * ## Endpoints
 *
 * All our APIs can be accessed through HTTP requests to URLs like:
 *
 * ```
 * https://api.samsara.com/<endpoint>
 * ```
 *
 * For EU customers, this URL will be:
 *
 * ```
 * https://api.eu.samsara.com/<endpoint>
 * ```
 *
 * <n class="warning">
 * <nh>
 * <i class="fa fa-exclamation-circle"></i>
 * Note
 * </nh>
 * <nb>
 * Legacy endpoints will have the URL: `https://api.samsara.com/v1/<endpoint>` or `https://api.eu.samsara.com/v1/<endpoint>`
 * </nb>
 * </n>
 *
 * ## Authentication
 *
 * To authenticate your API request you will need to include your secret token. You can manage your API tokens in the [Dashboard](https://cloud.samsara.com). They are visible under `Settings->Organization->API Tokens`.
 *
 * Your API tokens carry many privileges, so be sure to keep them secure. Do not share your secret API tokens in publicly accessible areas such as GitHub, client-side code, and so on.
 *
 * Authentication to the API is performed via Bearer Token in the HTTP Authorization header. Provide your API token as the `access_token` value in an `Authorization: Bearer` header. You do not need to provide a password:
 *
 * ```curl
 * Authorization: Bearer {access_token}
 * ```
 *
 * All API requests must be made over [HTTPS](https://en.wikipedia.org/wiki/HTTPS). Calls made over plain HTTP or without authentication will fail.
 *
 * ### OAuth2
 * If building an application for our marketplace, our API is accessible via. OAuth2 as well.
 *
 * | Type  | Value |
 * | ------------- |:-------------:|
 * | Security scheme      | OAuth2                                   |
 * | OAuth2 Flow          | accessCode                               |
 * | Authorization URL    | https://api.samsara.com/oauth2/authorize |
 * | Token URL            | https://api.samsara.com/oauth2/token     |
 *
 *
 *
 * ## Common Patterns
 *
 * You can find more info about request methods, response codes, error codes, versioning, pagination, timestamps, and mini-objects [here](https://developers.samsara.com/docs/common-structures).
 */
class Samsara extends Connector
{
    public function __construct(protected ?string $token) {}

    public function addresses(): Addresses
    {
        return new Addresses($this);
    }

    public function alerts(): Alerts
    {
        return new Alerts($this);
    }

    public function assets(): Assets
    {
        return new Assets($this);
    }

    public function attributes(): Attributes
    {
        return new Attributes($this);
    }

    public function betaApis(): BetaApis
    {
        return new BetaApis($this);
    }

    public function carrierProposedAssignments(): CarrierProposedAssignments
    {
        return new CarrierProposedAssignments($this);
    }

    public function coaching(): Coaching
    {
        return new Coaching($this);
    }

    public function contacts(): Contacts
    {
        return new Contacts($this);
    }

    public function documents(): Documents
    {
        return new Documents($this);
    }

    public function driverQrCodes(): DriverQrCodes
    {
        return new DriverQrCodes($this);
    }

    public function drivers(): Drivers
    {
        return new Drivers($this);
    }

    public function driverVehicleAssignments(): DriverVehicleAssignments
    {
        return new DriverVehicleAssignments($this);
    }

    public function equipment(): Equipment
    {
        return new Equipment($this);
    }

    public function fuelAndEnergy(): FuelAndEnergy
    {
        return new FuelAndEnergy($this);
    }

    public function gateways(): Gateways
    {
        return new Gateways($this);
    }

    /**
     * Determine if a token value has been set.
     *
     * @return bool
     */
    public function hasToken()
    {
        return ! is_null($this->token);
    }

    public function hoursOfService(): HoursOfService
    {
        return new HoursOfService($this);
    }

    public function idling(): Idling
    {
        return new Idling($this);
    }

    public function ifta(): Ifta
    {
        return new Ifta($this);
    }

    public function industrial(): Industrial
    {
        return new Industrial($this);
    }

    public function legacyApis(): LegacyApis
    {
        return new LegacyApis($this);
    }

    public function liveSharingLinks(): LiveSharingLinks
    {
        return new LiveSharingLinks($this);
    }

    public function locationAndSpeed(): LocationAndSpeed
    {
        return new LocationAndSpeed($this);
    }

    public function maintenance(): Maintenance
    {
        return new Maintenance($this);
    }

    public function mediaRetrievals(): MediaRetrievals
    {
        return new MediaRetrievals($this);
    }

    public function messages(): Messages
    {
        return new Messages($this);
    }

    public function organizationInfo(): OrganizationInfo
    {
        return new OrganizationInfo($this);
    }

    public function previewApis(): PreviewApis
    {
        return new PreviewApis($this);
    }

    public function resolveBaseUrl(): string
    {
        return 'https://api.samsara.com/';
    }

    public function routes(): Routes
    {
        return new Routes($this);
    }

    public function safety(): Safety
    {
        return new Safety($this);
    }

    public function sensors(): Sensors
    {
        return new Sensors($this);
    }

    public function settings(): Settings
    {
        return new Settings($this);
    }

    public function speedingIntervals(): SpeedingIntervals
    {
        return new SpeedingIntervals($this);
    }

    public function tachographEuOnly(): TachographEuOnly
    {
        return new TachographEuOnly($this);
    }

    public function tags(): Tags
    {
        return new Tags($this);
    }

    public function trailerAssignments(): TrailerAssignments
    {
        return new TrailerAssignments($this);
    }

    public function trailers(): Trailers
    {
        return new Trailers($this);
    }

    public function trips(): Trips
    {
        return new Trips($this);
    }

    public function users(): Users
    {
        return new Users($this);
    }

    public function vehicleLocations(): VehicleLocations
    {
        return new VehicleLocations($this);
    }

    public function vehicles(): Vehicles
    {
        return new Vehicles($this);
    }

    public function vehicleStats(): VehicleStats
    {
        return new VehicleStats($this);
    }

    public function webhooks(): Webhooks
    {
        return new Webhooks($this);
    }

    /**
     * Manually set the API token to be used with requests.
     *
     * @param  string  $token
     * @return $this
     */
    public function withToken(string $token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Default authenticator used.
     *
     * @return \Saloon\Http\Auth\TokenAuthenticator
     */
    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator($this->token);
    }

    /**
     * Default Request Headers.
     *
     * @return array<string, mixed>
     */
    protected function defaultHeaders(): array
    {
        return [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }
}
