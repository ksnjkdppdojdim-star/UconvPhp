<?php

namespace Uconv\Tests;

use PHPUnit\Framework\TestCase;
use Uconv\Counter;
use Uconv\Uconv;
use Uconv\Exceptions\UnknownUnitException;
use Uconv\Exceptions\InvalidInputException;
use Uconv\Exceptions\IncompatibleUnitsException;

class UconvTest extends TestCase
{
    protected function setUp(): void
    {
        Counter::resetAll();
    }

    public function testDistanceKmToM()
    {
        $this->assertEquals(10000, Uconv::convert('10km', 'm'));
    }

    public function testDistanceFtToM()
    {
        $this->assertEqualsWithDelta(3.048, Uconv::convert('10ft', 'm'), 0.001);
    }

    public function testDistanceInToCm()
    {
        $this->assertEqualsWithDelta(30.48, Uconv::convert('12in', 'cm'), 0.01);
    }

    public function testDistanceDecimal()
    {
        $this->assertEquals(5500, Uconv::convert('5.5km', 'm'));
    }

    public function testWeightLbsToKg()
    {
        $this->assertEqualsWithDelta(2.26796, Uconv::convert('5lbs', 'kg'), 0.00001);
    }

    public function testWeightOzToG()
    {
        $this->assertEqualsWithDelta(453.592, Uconv::convert('16oz', 'g'), 0.001);
    }

    public function testWeightKgToLb()
    {
        $this->assertEqualsWithDelta(2.20462, Uconv::convert('1kg', 'lb'), 0.00001);
    }

    public function testTimeHrToMin()
    {
        $this->assertEquals(60, Uconv::convert('1hr', 'min'));
    }

    public function testTimeDayToHr()
    {
        $this->assertEquals(24, Uconv::convert('1day', 'hr'));
    }

    public function testTimeMinToS()
    {
        $this->assertEquals(300, Uconv::convert('5min', 's'));
    }

    public function testCurrencyUsdToEur()
    {
        $result = Uconv::convert('100usd', 'eur');
        $this->assertEqualsWithDelta(85, $result, 1); // tolerance hardcoded
    }

    public function testCurrencyEurToGbp()
    {
        $result = Uconv::convert('100eur', 'gbp');
        $this->assertIsFloat($result);
    }

    public function testUnknownUnitError()
    {
        $this->expectException(UnknownUnitException::class);
        Uconv::convert('10xyz', 'm');
        $this->expectException(UnknownUnitException::class);
        Uconv::convert('10m', 'xyz');
    }

    public function testInvalidInputError()
    {
        $this->expectException(InvalidInputException::class);
        Uconv::convert('invalid', 'm');
        $this->expectException(InvalidInputException::class);
        Uconv::convert('', 'm');
        $this->expectException(InvalidInputException::class);
        Uconv::convert('10', 'm');
    }

    public function testIncompatibleUnitsError()
    {
        $this->expectException(IncompatibleUnitsException::class);
        Uconv::convert('10km', 'kg');
        $this->expectException(IncompatibleUnitsException::class);
        Uconv::convert('5lbs', 'hr');
        $this->expectException(IncompatibleUnitsException::class);
        Uconv::convert('100usd', 'm');
    }

    public function testParseVariants()
    {
        $this->assertEquals(10000, Uconv::convert('10 km', 'm'));
        $this->assertEquals(10500, Uconv::convert('10.5km', 'm'));
        $this->assertEquals(10000, Uconv::convert(' 10km ', 'm'));
        $this->assertEquals(-10000, Uconv::convert('-10km', 'm'));
    }

    public function testRequestCounterCountsConversions()
    {
        $this->assertSame(0, Uconv::getRequestCount());

        Uconv::convert('10km', 'm');
        Uconv::convert('1hr', 'min');

        $this->assertSame(2, Uconv::getRequestCount());
    }

    public function testRequestCounterCountsInvalidRequests()
    {
        try {
            Uconv::convert('invalid', 'm');
        } catch (InvalidInputException $e) {
            // Expected.
        }

        $this->assertSame(1, Uconv::getRequestCount());
    }

    public function testRequestCounterCanBeIncrementedManually()
    {
        $this->assertSame(1, Uconv::countRequest());
        $this->assertSame(2, Uconv::countRequest());
        $this->assertSame(2, Uconv::getRequestCount());
        $this->assertSame(0, Uconv::resetRequestCount());
    }

    public function testCounterCountsEachTextIndependently()
    {
        $this->assertSame(1, Counter::increment('server.request'));
        $this->assertSame(2, Counter::increment('server.request'));
        $this->assertSame(1, Counter::increment('loop.iteration'));

        $this->assertSame(2, Counter::get('server.request'));
        $this->assertSame(1, Counter::get('loop.iteration'));
        $this->assertSame(0, Counter::get('unknown.text'));
    }

    public function testCounterCanReturnAllCounts()
    {
        Counter::increment('first');
        Counter::increment('second');
        Counter::increment('second');

        $this->assertSame([
            'first' => 1,
            'second' => 2,
        ], Counter::all());
    }

    public function testCounterRejectsEmptyText()
    {
        $this->expectException(\InvalidArgumentException::class);

        Counter::increment('');
    }
}
