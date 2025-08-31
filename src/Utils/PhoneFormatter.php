<?php

namespace Innoboxrr\Support\Utils;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;
use libphonenumber\CountryCodeToRegionCodeMap;

class PhoneFormatter
{

    public static function format(string $number, ?string $region = null): array
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
      
        try {
            $parsedNumber = $phoneUtil->parse($number, $region);

            return [
                'countryCode' => $parsedNumber->getCountryCode(),
                'number' => $parsedNumber->getNationalNumber(),
                'region' => $phoneUtil->getRegionCodeForNumber($parsedNumber),
                'formattedE164' => $phoneUtil->format($parsedNumber, PhoneNumberFormat::E164),
                'formattedInternational' => $phoneUtil->format($parsedNumber, PhoneNumberFormat::INTERNATIONAL),
                'formattedNational' => $phoneUtil->format($parsedNumber, PhoneNumberFormat::NATIONAL),
            ];
        } catch (NumberParseException $e) {
            return ['error' => 'Invalid number: ' . $e->getMessage()];
        }
    }

    public static function formattedPhone(string $number, ?string $region = null): ?string
    {
        $formatter = self::format($number, $region);
        return isset($formatter['error']) ? null : "{$formatter['countryCode']} {$formatter['number']}";
    }

    public static function getDialCode($phoneNumber, array $possibleCountries = [], ?string $ip = null)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        foreach ($possibleCountries as $country) {
            try {
                $number = $phoneUtil->parse($phoneNumber, $country);
                if ($phoneUtil->isValidNumber($number)) {
                    return $number->getCountryCode();
                }
            } catch (NumberParseException $e) {
                continue;
            }
        }

        if ($ip) {
            $countryCode = self::getCountryFromIP($ip);
            if ($countryCode) {
                try {
                    $number = $phoneUtil->parse($phoneNumber, $countryCode);
                    if ($phoneUtil->isValidNumber($number)) {
                        return $number->getCountryCode();
                    }
                } catch (NumberParseException $e) {
                    return null;
                }
            }
        }

        return null;
    }

    private static function getCountryFromIP(string $ip): ?string
    {
        try {
            $response = file_get_contents("http://ip-api.com/json/{$ip}?fields=countryCode");
            $data = json_decode($response, true);
            return $data['countryCode'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getCountryCodeFromDialCode(int $dialCode): ?string
    {
        $countryMap = CountryCodeToRegionCodeMap::$countryCodeToRegionCodeMap[$dialCode] ?? null;
        return $countryMap ? $countryMap[0] : null;   
    }

    public static function normalizePhone(?string $phoneNumber, ?string $countryCode, ?string $ip): ?string
    {
        if (!$phoneNumber) {
            return null;
        }

        $dialCode = self::getDialCode($phoneNumber, [$countryCode], $ip);

        if (!$dialCode) {
            return $phoneNumber;
        }

        $countryCode = $countryCode ?? self::getCountryCodeFromDialCode($dialCode);

        return self::formattedPhone($phoneNumber, $countryCode);
    }
}
