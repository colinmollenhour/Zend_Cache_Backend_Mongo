<?php
/*
==New BSD License==

Copyright (c) 2013, Colin Mollenhour
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * The name of Colin Mollenhour may not be used to endorse or promote products
      derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

require_once 'app/Mage.php';
require_once 'Zend/Cache.php';
require_once 'Cm/Cache/Backend/Mongo.php';
require_once 'CommonExtendedBackendTest.php';

/**
 * @copyright  Copyright (c) 2013 Colin Mollenhour (http://colin.mollenhour.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cache_MongoBackendTest extends Zend_Cache_CommonExtendedBackendTest {

    /** @var Cm_Cache_Backend_Mongo */
    protected $_instance;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct('Cm_Cache_Backend_Mongo', $data, $dataName);
    }

    public function setUp($notag = false)
    {
        $this->_instance = new Cm_Cache_Backend_Mongo(array(
            'dbname' => 'cm_cache_test'
        ));
        $this->_instance->clean(Zend_Cache::CLEANING_MODE_ALL);
        parent::setUp($notag);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->_instance);
    }

    public function testConstructorCorrectCall()
    {
        // nah
    }

    public function testGetWithAnExpiredCacheId()
    {
        // not supported
    }

    /**
            $this->_instance->save('bar : data to cache', 'bar', array('tag3', 'tag4'));
            $this->_instance->save('bar2 : data to cache', 'bar2', array('tag3', 'tag1'));
            $this->_instance->save('bar3 : data to cache', 'bar3', array('tag2', 'tag3'));
     */
    public function testGetIdsMatchingAnyTags()
    {
        $res = $this->_instance->getIdsMatchingAnyTags(array('tag999'));
        $this->assertEquals(0, count($res));
    }

    public function testGetIdsMatchingAnyTags2()
    {
        $res = $this->_instance->getIdsMatchingAnyTags(array('tag1', 'tag999'));
        $this->assertEquals(1, count($res));
        $this->assertTrue(in_array('bar2', $res));
    }

    public function testGetIdsMatchingAnyTags3()
    {
        $res = $this->_instance->getIdsMatchingAnyTags(array('tag3', 'tag999'));
        $this->assertEquals(3, count($res));
        $this->assertTrue(in_array('bar', $res));
        $this->assertTrue(in_array('bar2', $res));
        $this->assertTrue(in_array('bar3', $res));
    }

    public function testGetIdsMatchingAnyTags4()
    {
        $res = $this->_instance->getIdsMatchingAnyTags(array('tag1', 'tag4'));
        $this->assertEquals(2, count($res));
        $this->assertTrue(in_array('bar', $res));
        $this->assertTrue(in_array('bar2', $res));
    }

    public function testCleanModeMatchingAnyTags()
    {
        $this->_instance->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array('tag999'));
        $this->assertTrue(!!$this->_instance->load('bar'));
        $this->assertTrue(!!$this->_instance->load('bar2'));
        $this->assertTrue(!!$this->_instance->load('bar3'));
    }

    public function testCleanModeMatchingAnyTags2()
    {
        $this->_instance->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array('tag1', 'tag999'));
        $this->assertTrue(!!$this->_instance->load('bar'));
        $this->assertFalse(!!$this->_instance->load('bar2'));
        $this->assertTrue(!!$this->_instance->load('bar3'));
    }

    public function testCleanModeMatchingAnyTags3()
    {
        $this->_instance->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array('tag3', 'tag999'));
        $this->assertFalse(!!$this->_instance->load('bar'));
        $this->assertFalse(!!$this->_instance->load('bar2'));
        $this->assertFalse(!!$this->_instance->load('bar3'));
    }

    public function testCleanModeMatchingAnyTags4()
    {
        $this->_instance->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array('tag1', 'tag4'));
        $this->assertFalse(!!$this->_instance->load('bar'));
        $this->assertFalse(!!$this->_instance->load('bar2'));
        $this->assertTrue(!!$this->_instance->load('bar3'));
    }

}