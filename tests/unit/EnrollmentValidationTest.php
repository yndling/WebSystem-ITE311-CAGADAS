<?php

use App\Models\EnrollmentModel;

// If PHPUnit is not available in this environment, provide a tiny TestCase polyfill
if (!class_exists('PHPUnit\\Framework\\TestCase')) {
    class TestCase
    {
        protected function invokeValidateSchedule(string $schedule): bool
        {
            // Use reflection to call the model method without invoking CI constructor
            // Instantiate the model and call the public method directly
            $model = new \App\Models\EnrollmentModel();
            return (bool) $model->validateScheduleFormat($schedule);
        }

        public function assertTrue($cond)
        {
            if (!$cond) {
                throw new Exception('Assertion failed: expected true');
            }
        }

        public function assertFalse($cond)
        {
            if ($cond) {
                throw new Exception('Assertion failed: expected false');
            }
        }
    }
}

class EnrollmentValidationTest extends TestCase
{
    public function testValidateScheduleFormatValidExamples()
    {
        $model = new EnrollmentModel();

        $this->assertTrue($model->validateScheduleFormat('MWF 9:00-10:30'));
        $this->assertTrue($model->validateScheduleFormat('TTH 14:00-15:30'));
        $this->assertTrue($model->validateScheduleFormat('M 08:00-09:00'));
    }

    public function testValidateScheduleFormatInvalidExamples()
    {
        $model = new EnrollmentModel();

        $this->assertFalse($model->validateScheduleFormat('Monday 9:00-10:30'));
        $this->assertFalse($model->validateScheduleFormat('MWF9:00-10:30'));
        $this->assertFalse($model->validateScheduleFormat('MWF 9-10'));
        $this->assertFalse($model->validateScheduleFormat(''));
    }
}
