import 'package:device_preview/device_preview.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:e_askep_mobile/main.dart';

void main() {
  testWidgets('Welcome screen renders e-Askep title and login buttons', (WidgetTester tester) async {
    await tester.pumpWidget(
      DevicePreview(
        enabled: false,
        builder: (context) => const EAskepApp(),
      ),
    );

    expect(find.text('e-Askep Riau'), findsOneWidget);
    expect(find.text('Masuk Mahasiswa'), findsOneWidget);
    expect(find.text('Masuk Dosen / CI'), findsOneWidget);
  });
}
