export function useCurrency() {
    const formatter = new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        maximumFractionDigits: 0,
    });

    function money(value: number | string | null | undefined): string {
        return formatter.format(Number(value ?? 0));
    }

    return { money };
}
