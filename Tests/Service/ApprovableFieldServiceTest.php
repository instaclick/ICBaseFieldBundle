<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use IC\Bundle\Core\FieldBundle\Service\ApprovableFieldService;

/**
 * ApprovableFieldService test
 *
 * @group Unit
 * @group Service
 *
 * @author Yuan Xie <yuanxie@live.ca>
 * @author David Maignan <davidm@gmail.com>
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class ApprovableFieldServiceTest extends TestCase
{
    /**
     * @var \IC\Bundle\Base\ApprovableBundle\Service\ApprovableFieldService
     */
    private $service;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->service = new ApprovableFieldService();
    }

    /**
     * Test mergeContent
     *
     * @param string $content          Content
     * @param string $overwriteContent Overwrite content
     * @param string $expectedResult   Expected result
     *
     * @dataProvider provideDataForTestMergeContent
     */
    public function testMergeContent($content, $overwriteContent, $expectedResult)
    {
        $result = $this->service->mergeContent($content, $overwriteContent);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Data provider for testMergeContent
     *
     * @return array
     */
    public function provideDataForTestMergeContent()
    {
        $variation1 = json_encode(array(
            array(
                'field'      => 123,
                'choiceList' => array(),
                'value'      => 'first',
            )
        ));

        $variation2 = json_encode(array(
            array(
                'field'      => 123,
                'choiceList' => array(),
                'value'      => 'second',
            )
        ));

        $variation3Original = json_encode(array(
            array(
                'field'      => 1,
                'choiceList' => array(),
                'value'      => 'elephant',
            ),
            array(
                'field'      => 3,
                'choiceList' => array(),
                'value'      => 'snake',
            ),
            array(
                'field'      => 5,
                'choiceList' => array(),
                'value'      => 'gem',
            ),
        ));

        $variation3Updated = json_encode(array(
            array(
                'field'      => 2,
                'choiceList' => array(),
                'value'      => 'go',
            ),
            array(
                'field'      => 3,
                'choiceList' => array(),
                'value'      => 'python',
            ),
            array(
                'field'      => 4,
                'choiceList' => array(),
                'value'      => 'java',
            ),
            array(
                'field'      => 5,
                'choiceList' => array(),
                'value'      => 'ruby',
            ),
        ));

        $variation3Merged = json_encode(array(
            array(
                'field'      => 1,
                'choiceList' => array(),
                'value'      => 'elephant',
            ),
            array(
                'field'      => 3,
                'choiceList' => array(),
                'value'      => 'python',
            ),
            array(
                'field'      => 5,
                'choiceList' => array(),
                'value'      => 'ruby',
            ),
            array(
                'field'      => 2,
                'choiceList' => array(),
                'value'      => 'go',
            ),
            array(
                'field'      => 4,
                'choiceList' => array(),
                'value'      => 'java',
            ),
        ));

        $testCaseList = array();

        $testCaseList[0] = array(
            'content'          => '',
            'overwriteContent' => '',
            'expectedResult'   => '',
        );

        $testCaseList[1] = array(
            'content'          => '',
            'overwriteContent' => $variation1,
            'expectedResult'   => $variation1,
        );

        $testCaseList[2] = array(
            'content'          => $variation1,
            'overwriteContent' => '',
            'expectedResult'   => $variation1,
        );

        $testCaseList[3] = array(
            'content'          => $variation1,
            'overwriteContent' => $variation2,
            'expectedResult'   => $variation2,
        );

        $testCaseList[4] = array(
            'content'          => $variation3Original,
            'overwriteContent' => $variation3Updated,
            'expectedResult'   => $variation3Merged,
        );

        return $testCaseList;
    }

    /**
     * Test absorbPersistableContent
     *
     * @param string $content         Content
     * @param string $incomingContent Incoming content
     * @param string $expectedResult  Expected result
     *
     * @dataProvider provideDataForTestAbsorbPersistableContent
     */
    public function testAbsorbPersistableContent($content, $incomingContent, $expectedResult)
    {
        $result = $this->service->absorbPersistableContent($content, $incomingContent);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Data provider for testAbsorbPersistableContent
     *
     * @return array
     */
    public function provideDataForTestAbsorbPersistableContent()
    {
        $variation1Base = json_encode(array(
            array(
                'field'      => 1,
                'choiceList' => array(),
                'value'      => 'one',
            ),
        ));

        $variation1EmptyValue = json_encode(array(
            array(
                'field'      => 1,
                'choiceList' => array(),
                'value'      => '',
            ),
        ));

        $variation2 = json_encode(array(
            array(
                'field'      => 1,
                'choiceList' => array(),
                'value'      => 'one',
            ),
            array(
                'field'      => 2,
                'choiceList' => array(1, 3, 5),
                'value'      => null,
            ),
        ));

        $variation3 = json_encode(array(
            array(
                'field'      => 1,
                'choiceList' => array(),
                'value'      => 'one',
            ),
            array(
                'field'      => 2,
                'choiceList' => array(1, 3, 5),
                'value'      => null,
            ),
        ));

        $testCaseList = array();

        $testCaseList[0] = array(
            'content'         => '',
            'incomingContent' => '',
            'expectedResult'  => '',
        );

        $testCaseList[1] = array(
            'content'         => '',
            'incomingContent' => $variation1Base,
            'expectedResult'  => '[]',
        );

        $testCaseList[2] = array(
            'content'         => '',
            'incomingContent' => $variation1EmptyValue,
            'expectedResult'  => $variation1EmptyValue,
        );

        $testCaseList[3] = array(
            'content'         => $variation1Base,
            'incomingContent' => $variation1EmptyValue,
            'expectedResult'  => $variation1EmptyValue,
        );

        $testCaseList[4] = array(
            'content'         => $variation1Base,
            'incomingContent' => $variation2,
            'expectedResult'  => $variation3,
        );

        return $testCaseList;
    }

    /**
     * Test cancelContent
     *
     * @param string $content        Content
     * @param string $cancelContent  Cancel content
     * @param string $expectedResult Expected result
     *
     * @dataProvider provideDataForTestCancelContent
     */
    public function testCancelContent($content, $cancelContent, $expectedResult)
    {
        $result = $this->service->cancelContent($content, $cancelContent);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Data provider for testCancelContent
     *
     * @return array
     */
    public function provideDataForTestCancelContent()
    {
        $contentOne = array(
            'field' => 1,
            'choiceList' => array(),
            'value' => 'A'
        );

        $contentOneEmpty = array(
            'field' => 1,
            'choiceList' => array(),
            'value' => ''
        );

        $contentTwo = array(
            'field' => 2,
            'choiceList' => array(1,2),
            'value' => null
        );

        $testCaseList = array();

        $testCaseList[0] = array(
            'content'         => '',
            'incomingContent' => '',
            'expectedResult'  => '[]',
        );

        $testCaseList[1] = array(
            'content'         => json_encode(array($contentOne)),
            'incomingContent' => json_encode(array($contentOne)),
            'expectedResult'  => '[]',
        );

        $testCaseList[2] = array(
            'content'         => json_encode(array($contentOne)),
            'incomingContent' => '',
            'expectedResult'  => json_encode(array($contentOne)),
        );

        $testCaseList[3] = array(
            'content'         => json_encode(array($contentOne)),
            'incomingContent' => json_encode(array($contentOneEmpty)),
            'expectedResult'  => json_encode(array($contentOne)),
        );

        $testCaseList[4] = array(
            'content'         => json_encode(array($contentOne, $contentTwo)),
            'incomingContent' => json_encode(array($contentTwo)),
            'expectedResult'  => json_encode(array($contentOne)),
        );

        return $testCaseList;
    }
}
