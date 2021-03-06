<?php

/**
 * CreateContestTest
 *
 * @author joemmanuel
 */

class CreateContestTest extends \OmegaUp\Test\ControllerTestCase {
    /**
     * Basic Create Contest scenario
     *
     */
    public function testCreateContestPositive() {
        // Create a valid contest Request object
        $contestData = \OmegaUp\Test\Factories\Contest::getRequest(new \OmegaUp\Test\Factories\ContestParams(
            ['admissionMode' => 'private']
        ));
        $r = $contestData['request'];
        $contestDirector = $contestData['director'];

        // Log in the user and set the auth token in the new request
        $login = self::login($contestDirector);
        $r['auth_token'] = $login->auth_token;

        // Call the API
        $response = \OmegaUp\Controllers\Contest::apiCreate($r);

        // Assert status of new contest
        $this->assertEquals('ok', $response['status']);

        // Assert that the contest requested exists in the DB
        $this->assertContest($r);
    }

    /**
     * Tests that missing params throw exception
     */
    public function testMissingParameters() {
        // Array of valid keys
        $valid_keys = [
            'title',
            'description',
            'start_time',
            'finish_time',
            'alias',
            'points_decay_factor',
            'submissions_gap',
            'feedback',
            'scoreboard',
            'penalty_type',
        ];

        foreach ($valid_keys as $key) {
            // Create a valid contest Request object
            $contestData = \OmegaUp\Test\Factories\Contest::getRequest(new \OmegaUp\Test\Factories\ContestParams(
                ['admissionMode' => 'private']
            ));
            $r = $contestData['request'];
            $contestDirector = $contestData['director'];

            $login = self::login($contestDirector);

            // unset the current key from request
            unset($r[$key]);

            // Set the valid auth token in the new request
            $r['auth_token'] = $login->auth_token;

            try {
                // Call the API
                $response = \OmegaUp\Controllers\Contest::apiCreate($r);
            } catch (\OmegaUp\Exceptions\InvalidParameterException $e) {
                // This exception is expected
                unset($_REQUEST);
                continue;
            }

            $this->fail('Exception was expected. Parameter: ' . $key);
        }
    }

    /**
     * Tests that 2 contests with same name cannot be created
     *
     * @expectedException \OmegaUp\Exceptions\DuplicatedEntryInDatabaseException
     */
    public function testCreate2ContestsWithSameAlias() {
        // Create a valid contest Request object
        $contestData = \OmegaUp\Test\Factories\Contest::getRequest(new \OmegaUp\Test\Factories\ContestParams(
            ['admissionMode' => 'private']
        ));
        $r = $contestData['request'];
        $contestDirector = $contestData['director'];

        // Log in the user and set the auth token in the new request
        $login = self::login($contestDirector);
        $r['auth_token'] = $login->auth_token;

        // Call the API
        $response = \OmegaUp\Controllers\Contest::apiCreate($r);
        $this->assertEquals('ok', $response['status']);

        // Call the API for the 2nd time with same alias
        $response = \OmegaUp\Controllers\Contest::apiCreate($r);
    }

    /**
     * Tests very long contests
     *
     * @expectedException \OmegaUp\Exceptions\InvalidParameterException
     */
    public function testCreateVeryLongContest() {
        // Create a valid contest Request object
        $contestData = \OmegaUp\Test\Factories\Contest::getRequest(new \OmegaUp\Test\Factories\ContestParams(
            ['admissionMode' => 'private']
        ));
        $r = $contestData['request'];
        $contestDirector = $contestData['director'];

        // Longer than a month
        $r['finish_time'] = $r['start_time'] + (60 * 60 * 24 * 32);

        // Log in the user and set the auth token in the new request
        $login = self::login($contestDirector);
        $r['auth_token'] = $login->auth_token;

        // Call the API
        $response = \OmegaUp\Controllers\Contest::apiCreate($r);
    }

    /**
     * Public contest without problems is not valid.
     *
     * @expectedException \OmegaUp\Exceptions\InvalidParameterException
     */
    public function testCreatePublicContest() {
        // Create a valid contest Request object
        $contestData = \OmegaUp\Test\Factories\Contest::getRequest(new \OmegaUp\Test\Factories\ContestParams(
            ['admissionMode' => 'private']
        ));
        $r = $contestData['request'];
        $contestDirector = $contestData['director'];
        $r['admission_mode'] = 'public';

        // Log in the user and set the auth token in the new request
        $login = self::login($contestDirector);
        $r['auth_token'] = $login->auth_token;

        // Call the API
        $response = \OmegaUp\Controllers\Contest::apiCreate($r);
        $this->assertEquals('ok', $response['status']);
    }

    /**
     * Public contest with problems NOW is NOT valid. You need
     * to create the contest first and then you can add problems
     *
     * @expectedException \OmegaUp\Exceptions\InvalidParameterException
     */
    public function testCreatePublicContestWithProblems() {
        $problem = \OmegaUp\Test\Factories\Problem::createProblem();

        // Create a valid contest Request object
        $contestData = \OmegaUp\Test\Factories\Contest::getRequest(new \OmegaUp\Test\Factories\ContestParams(
            ['admissionMode' => 'private']
        ));
        $r = $contestData['request'];
        $contestDirector = $contestData['director'];
        $r['admission_mode'] = 'public';
        $r['problems'] = json_encode([[
            'problem' => $problem['problem']->alias,
            'points' => 100,
        ]]);

        // Log in the user and set the auth token in the new request
        $login = self::login($contestDirector);
        $r['auth_token'] = $login->auth_token;

        // Call the API
        $response = \OmegaUp\Controllers\Contest::apiCreate($r);
    }

    /**
     * Public contest with private problems is not valid.
     *
     * @expectedException \OmegaUp\Exceptions\ForbiddenAccessException
     */
    public function testCreatePublicContestWithPrivateProblems() {
        $problem = \OmegaUp\Test\Factories\Problem::createProblem(new \OmegaUp\Test\Factories\ProblemParams([
            'visibility' => 0
        ]));

        // Create a valid contest Request object
        $contestData = \OmegaUp\Test\Factories\Contest::getRequest(new \OmegaUp\Test\Factories\ContestParams(
            ['admissionMode' => 'private']
        ));
        $r = $contestData['request'];
        $contestDirector = $contestData['director'];
        $r['admission_mode'] = 'public';
        $r['problems'] = json_encode([[
            'problem' => $problem['problem']->alias,
            'points' => 100,
        ]]);

        // Log in the user and set the auth token in the new request
        $login = self::login($contestDirector);
        $r['auth_token'] = $login->auth_token;

        // Call the API
        $response = \OmegaUp\Controllers\Contest::apiCreate($r);
    }

    /**
     * Creates a contest with window length, and review contestant
     * only can create run inside sumbission window
     */
    public function testCreateContestWithWindowLength() {
        // Get a problem
        $problem = \OmegaUp\Test\Factories\Problem::createProblem();

        $originalTime = \OmegaUp\Time::get();

        // Create contest with 2 hours and a window length 30 of minutes
        $contest = \OmegaUp\Test\Factories\Contest::createContest(
            new \OmegaUp\Test\Factories\ContestParams([
                'windowLength' => 30,
                'startTime' => $originalTime,
                'finishTime' => $originalTime + 60 * 2 * 60,
            ])
        );

        // Add the problem to the contest
        \OmegaUp\Test\Factories\Contest::addProblemToContest(
            $problem,
            $contest
        );

        // Create a contestant
        ['user' => $contestant, 'identity' => $identity] = \OmegaUp\Test\Factories\User::createUser();

        // Add contestant to contest
        \OmegaUp\Test\Factories\Contest::addUser($contest, $identity);

        // User joins the contest 1 hour and 50 minutes after it starts
        $updatedTime = $originalTime + 110 * 60;
        \OmegaUp\Time::setTimeForTesting($updatedTime);
        \OmegaUp\Test\Factories\Contest::openContest($contest, $identity);
        \OmegaUp\Test\Factories\Contest::openProblemInContest(
            $contest,
            $problem,
            $identity
        );

        // User creates a run in a valid time
        $run = \OmegaUp\Test\Factories\Run::createRun(
            $problem,
            $contest,
            $identity
        );
        \OmegaUp\Test\Factories\Run::gradeRun($run, 1.0, 'AC', 10);

        try {
            // User tries to create a run 5 minutes after contest has finished
            $updatedTime = $updatedTime + 15 * 60;
            \OmegaUp\Time::setTimeForTesting($updatedTime);
            $run = \OmegaUp\Test\Factories\Run::createRun(
                $problem,
                $contest,
                $identity
            );
            \OmegaUp\Test\Factories\Run::gradeRun($run, 1.0, 'AC', 10);
            $this->fail(
                'Contestant should not create a run after contest finishes'
            );
        } catch (\OmegaUp\Exceptions\NotAllowedToSubmitException $e) {
            // Pass
            $this->assertEquals('runNotInsideContest', $e->getMessage());
        } finally {
            \OmegaUp\Time::setTimeForTesting($originalTime);
        }
    }
}
