<?php

namespace Rx\Functional\Operator;

use Rx\Functional\FunctionalTestCase;
use Rx\Observable;
use Rx\Observable\ReturnObservable;
use Rx\Observer\CallbackObserver;

class ElementAtTest extends FunctionalTestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_an_exception_on_negative_index()
    {
        $observable = new ReturnObservable(42);
        $result     = $observable->elementAt(-1);

        $result->subscribe(new CallbackObserver());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_an_exception_on_not_int_index()
    {
        $observable = new ReturnObservable(42);
        $result     = $observable->elementAt("a");

        $result->subscribe(new CallbackObserver());
    }

    /**
     * @test
     */
    public function it_calls_on_complete_after_value_at_index()
    {
        $xs = $this->createHotObservable(array(
            onNext(300, 21),
            onNext(500, 42),
            onNext(600, 14),
            onNext(700, 67),
            onNext(800, 84),
            onCompleted(820),
        ));

        $results = $this->scheduler->startWithCreate(function () use ($xs) {
            return $xs->elementAt(2);
        });

        $this->assertMessages(array(
            onNext(600, 14),
            onCompleted(600),
        ), $results->getMessages());

        $this->assertSubscriptions([
            subscribe(200,600)
        ], $xs->getSubscriptions());
    }

    /**
     * @test
     * @expectedException \OutOfRangeException
     */
    public function exception_when_index_greater_than_sequence_length()
    {
        Observable::fromArray([1, 2, 3])
            ->elementAt(3)
            ->subscribe(new CallbackObserver());
    }

    /**
     * @test
     */
    public function it_throws_exception_on_empty_sequence()
    {
        $error = new \OutOfRangeException("index out of range");

        $xs = $this->createHotObservable(array(
            onCompleted(230),
        ));

        $results = $this->scheduler->startWithCreate(function () use ($xs) {
            return $xs->elementAt(3);
        });

        $this->assertMessages(array(
            onError(231, $error),
        ), $results->getMessages());
    }

    /**
     * Adapted from RxJS
     *
     * @test
     */
    public function elementAt_First()
    {
        $xs = $this->createHotObservable([
            onNext(280, 42),
            onNext(360, 43),
            onNext(470, 44),
            onCompleted(600)
        ]);

        $results = $this->scheduler->startWithCreate(function () use ($xs) {
            return $xs->elementAt(0);
        });

        $this->assertMessages([
            onNext(280, 42),
            onCompleted(280)
        ], $results->getMessages());

        $this->assertSubscriptions([
            subscribe(200, 280)
        ], $xs->getSubscriptions());
    }

    /**
     * Adapted from RxJS
     *
     * @test
     */
    public function elementAt_Other()
    {
        $xs = $this->createHotObservable([
            onNext(280, 42),
            onNext(360, 43),
            onNext(470, 44),
            onCompleted(600)
        ]);

        $results = $this->scheduler->startWithCreate(function () use ($xs) {
            return $xs->elementAt(2);
        });

        $this->assertMessages([
            onNext(470, 44),
            onCompleted(470)
        ], $results->getMessages());

        $this->assertSubscriptions([
            subscribe(200, 470)
        ], $xs->getSubscriptions());
    }

    /**
     * Adapted from RxJS
     *
     * @test
     */
    public function elementAt_out_of_range()
    {
        $xs = $this->createHotObservable([
            onNext(280, 42),
            onNext(360, 43),
            onNext(470, 44),
            onCompleted(600)
        ]);

        $results = $this->scheduler->startWithCreate(function () use ($xs) {
            return $xs->elementAt(3);
        });

        $this->assertMessages([
            onError(601, new \OutOfRangeException())
        ], $results->getMessages());

        $this->assertSubscriptions([
            subscribe(200, 600)
        ], $xs->getSubscriptions());
    }

    /**
     * Adapted from RxJS
     *
     * This is not enabled because the RxPHP implementation does not accept 
     *
     * @test
     */
    public function elementAt_out_of_range_default()
    {
        $xs = $this->createHotObservable([
            onNext(280, 42),
            onNext(360, 43),
            onNext(470, 44),
            onCompleted(600)
        ]);

        $results = $this->scheduler->startWithCreate(function () use ($xs) {
            return $xs->elementAt(3, 84);
        });

        $this->assertMessages([
            onNext(601, 84),
            onCompleted(601)
        ], $results->getMessages());

        $this->assertSubscriptions([
            subscribe(200, 600)
        ], $xs->getSubscriptions());
    }
}
