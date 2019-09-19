<?php

namespace Odiseo\SyliusUpsPlugin\Calculator;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Ups\Entity\Address;
use Ups\Entity\Dimensions;
use Ups\Entity\Package;
use Ups\Entity\RatedShipment;
use Ups\Entity\RateInformation;
use Ups\Entity\RateResponse;
use Ups\Entity\Service;
use Ups\Entity\ShipFrom;
use Ups\Entity\Shipment;
use Ups\Entity\UnitOfMeasurement;
use Ups\Rate;

final class UpsCalculator implements CalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculate(ShipmentInterface $subject, array $configuration): int
    {
        if (!$subject instanceof \Sylius\Component\Core\Model\Shipment) {
            throw new UnexpectedTypeException($subject, 'ShipmentInterface');
        }

        /** @var ShippingMethodInterface $method */
        $method = $subject->getMethod();

        if (!$method instanceof ShippingMethodInterface) {
            throw new UnexpectedTypeException($method, 'ShippingMethodInterface');
        }

        /** @var OrderInterface $order */
        $order = $subject->getOrder();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException($order, 'OrderInterface');
        }

        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();

        if (!$channel instanceof ChannelInterface) {
            throw new UnexpectedTypeException($order, 'ChannelInterface');
        }

        $channelCode = $channel->getCode();
        if (!isset($configuration[$channelCode])) {
            throw new MissingChannelConfigurationException(sprintf(
                'Channel %s has no configuration defined for shipping method %s',
                $channel->getName(),
                $method->getName()
            ));
        }

        /** @var AddressInterface $shippingAddress */
        $shippingAddress = $order->getShippingAddress();

        if (!$shippingAddress instanceof AddressInterface) {
            return 0;
        }

        $packageValues = array_merge($this->getPackageValues($order->getItems()), array(
            'destination_country_code' => $shippingAddress->getCountryCode(),
            'destination_postcode' =>  $shippingAddress->getPostcode(),
            'destination_city' => $shippingAddress->getCity(),
            'destination_address' => $shippingAddress->getCountryCode()
        ));

        $packageValues['type'] = '02';

        $rateValue = 0;

        if ($packageValues['weight'] > 0) {
            $rateValue = $this->getUpsRateValue($configuration[$channelCode], $packageValues);
        }

        return intval($rateValue*100);
    }

    /**
     * @param Collection $items
     * @return array('width', 'height', 'length', 'weight', 'pounds', 'ounces')
     */
    private function getPackageValues(Collection $items)
    {
        $width = 0;
        $height = 0;
        $length = 0;
        $weight = 0;

        /** @var OrderItemInterface $item */
        foreach ($items as $item) {
            /** @var ProductVariantInterface $variant */
            $variant = $item->getVariant();

            $width += $item->getQuantity() * $variant->getWidth();
            $height += $item->getQuantity() * $variant->getHeight();
            $length += $item->getQuantity() * $variant->getDepth();
            $weight += $item->getQuantity() * $variant->getWeight();
        }

        $pounds = $weight*2.20462;
        $ounces = $weight*35.274;

        return array('width' => $width, 'height' => $height, 'length' => $length, 'weight' => $weight, 'pounds' => $pounds, 'ounces' => $ounces);
    }

    /**
     * @param string $origin // origin country code
     * @param string $destination // destination country code
     * @return string
     */
    private function getServiceCode(string $origin, string $destination)
    {
        $serviceCode = '01';
        if ($origin =='PR') {
            if ($destination == 'PR') {
                $serviceCode = '01'; // Next Day Air
            } elseif ($destination == 'US') {
                $serviceCode = '02'; // Second Day Air
            } else {
                $serviceCode = '65'; // Ups Saver
            }
        } elseif ($origin =='DR') {
            $serviceCode = '65'; // Ups Saver
        }

        return $serviceCode;
    }

    /**
     * @param array $configuration
     * @param array $packageValues
     * @return float
     * @throws \Exception
     */
    private function getUpsRateValue(array $configuration, array $packageValues)
    {
        $rate = new Rate(
            $configuration['accesskey'],
            $configuration['username'],
            $configuration['password']
        );

        $packageValues['account'] = $configuration['account'];
        $packageValues['origination_postcode'] = $configuration['origination_postcode'];
        $packageValues['origination_country_code'] = $configuration['origination_country_code'];
        $packageValues['origination_address'] = $configuration['origination_address'];
        $packageValues['origination_city'] = $configuration['origination_city'];
        $packageValues['service_code'] = $this->getServiceCode($packageValues['origination_country_code'], $packageValues['destination_country_code']);

        $shipment = new Shipment();

        $shipper = $shipment->getShipper();
        $shipper->setShipperNumber($packageValues['account']);

        $shipperAddress = $shipment->getShipper()->getAddress();
        $shipperAddress->setPostalCode($packageValues['origination_postcode']);
        $shipperAddress->setAddressLine1($packageValues['origination_address']);
        $shipperAddress->setCity($packageValues['origination_city']);
        $shipperAddress->setCountryCode($packageValues['origination_country_code']);

        $address = new Address();
        $address->setPostalCode($packageValues['origination_postcode']);
        $address->setCountryCode($packageValues['origination_country_code']);
        $address->setCity($packageValues['origination_city']);
        $address->setStateProvinceCode($packageValues['origination_city']);

        $shipFrom = new ShipFrom();
        $shipFrom->setAddress($address);

        $shipment->setShipFrom($shipFrom);

        $shipTo = $shipment->getShipTo();
        $shipToAddress = $shipTo->getAddress();
        $shipToAddress->setCountryCode($packageValues['destination_country_code']);
        $shipToAddress->setCity($packageValues['destination_city']);
        $shipToAddress->setPostalCode($packageValues['destination_postcode']);
        $shipToAddress->setResidentialAddressIndicator($packageValues['destination_address']);

        $pounds = $packageValues['weight'] *2.20462;

        $package = new Package();
        $package->getPackagingType()->setCode($packageValues['type']);


        $package->getPackageWeight()->setWeight((string)$pounds);
        $weightUnit = new UnitOfMeasurement();
        $weightUnit->setCode(UnitOfMeasurement::UOM_LBS);
        $package->getPackageWeight()->setUnitOfMeasurement($weightUnit);

        $dimensions = new Dimensions();
        $dimensions->setHeight($packageValues['height']);
        $dimensions->setWidth($packageValues['width']);
        $dimensions->setLength($packageValues['length']);

        $unit = new UnitOfMeasurement();
        $unit->setCode(UnitOfMeasurement::UOM_IN);

        $dimensions->setUnitOfMeasurement($unit);
        $package->setDimensions($dimensions);

        $shipment->addPackage($package);

        $service = new Service();
        $service->setCode($packageValues['service_code']);

        $shipment->setService($service);
        $object = new \stdClass();
        $object->NegotiatedRatesIndicator = true;

        $shipment->setRateInformation(new RateInformation($object));

        /** @var RateResponse $rateResponse */
        $rateResponse = $rate->getRate($shipment);

        /** @var RatedShipment $ratedShipment */
        $ratedShipment = $rateResponse->RatedShipment[0];

        if ($ratedShipment->NegotiatedRates) {
            $monetaryValue = $ratedShipment->NegotiatedRates->NetSummaryCharges->GrandTotal->MonetaryValue;
        } else {
            $monetaryValue = $ratedShipment->TotalCharges->MonetaryValue;
        }

        return $monetaryValue;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'ups';
    }
}
