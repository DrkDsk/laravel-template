import { computed, ref } from 'vue';
import type { CalculateStep } from '../types/calculate';

export const useCalculateSteps = ({
    steps,
    validateCurrentStep,
    submitCalculate,
}: {
    steps: CalculateStep[];
    validateCurrentStep: (step: number) => boolean;
    submitCalculate: () => void;
}) => {
    const currentStep = ref(1);

    const progressWidth = computed(
        () => `${((currentStep.value - 1) / (steps.length - 1)) * 100}%`,
    );

    const goToStep = (step: number) => {
        if (step <= currentStep.value) {
            currentStep.value = step;
        }
    };

    const goToPreviousStep = () => {
        currentStep.value = Math.max(currentStep.value - 1, 1);
    };

    const goToNextStep = () => {
        if (currentStep.value === 1) {
            if (validateCurrentStep(currentStep.value)) {
                currentStep.value = 2;
            }

            return;
        }

        if (currentStep.value === steps.length) {
            if (!validateCurrentStep(1)) {
                currentStep.value = 1;

                return;
            }

            submitCalculate();

            return;
        }

        currentStep.value = Math.min(currentStep.value + 1, steps.length);
    };

    const returnToClientStep = () => {
        currentStep.value = 1;
    };

    return {
        currentStep,
        progressWidth,
        goToStep,
        goToPreviousStep,
        goToNextStep,
        returnToClientStep,
    };
};
