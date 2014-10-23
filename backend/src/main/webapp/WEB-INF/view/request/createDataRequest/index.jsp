<html>
<head>
<title>Upload</title>
</head>
<body>
	<html:errors />
	<html:messages id="m" message="true">
${f:h(m)}<br />
	</html:messages>

	<s:form>
		<table>
		<tr>
			<th>リクエストID</th>
			<td><html:text property="requestId" size="70" value="${requestId}" /></td>
		</tr>
		<tr>
			<th>Cligen</th>
			<td>
				<input type="submit" name="addCligenPointRequest" value="Point指定要求情報登録" />
				<input type="submit" name="addCligenAreaRequest" value="Area指定要求情報登録" />
				<input type="submit" name="addCligenUserRequest" value="User指定要求情報登録" />
			</td>
		</tr>
		<tr>
			<th>Cdfdm</th>
			<td>
				<input type="submit" name="addCdfdmPointRequest" value="Point指定要求情報登録" />
				<input type="submit" name="addCdfdmAreaRequest" value="Area指定要求情報登録" />
				<input type="submit" name="addCdfdmUserRequest" value="User指定要求情報登録" />
			</td>
		</tr>
		</table>
		<input type="submit" name="send" value="データ生成要求" />
	</s:form>
</body>
</html>
